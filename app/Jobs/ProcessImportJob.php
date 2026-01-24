<?php

namespace App\Jobs;

use App\Models\Import;
use App\Services\Import\ExcelStreamReader;
use App\Services\Import\ImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Queue job for processing large imports asynchronously.
 *
 * Design decisions:
 * 1. Uses database queue for reliability and persistence
 * 2. Implements chunked processing via ExcelStreamReader
 * 3. Updates progress after each chunk for real-time UI updates
 * 4. Handles failures gracefully with retry support
 * 5. Memory-efficient: processes in chunks, clears caches periodically
 */
class ProcessImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     * Set to 3 for automatic retry on transient failures.
     */
    public int $tries = 3;

    /**
     * Maximum seconds the job can run.
     * Set to 0 for unlimited (handled by queue worker --timeout=0).
     */
    public int $timeout = 0;

    /**
     * Delete the job if its models no longer exist.
     */
    public bool $deleteWhenMissingModels = true;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    private Import $import;
    private string $filePath;
    private string $importType;
    private bool $skipErrors;
    private int $chunkSize;

    /**
     * Create a new job instance.
     */
    public function __construct(
        Import $import,
        string $filePath,
        string $importType = 'archive',
        bool $skipErrors = true,
        int $chunkSize = 100
    ) {
        $this->import = $import;
        $this->filePath = $filePath;
        $this->importType = $importType;
        $this->skipErrors = $skipErrors;
        $this->chunkSize = $chunkSize;

        // Use database queue for reliability
        $this->onQueue('imports');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Remove execution time limit for large imports
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        Log::info("[Import #{$this->import->id}] Starting import job", [
            'type' => $this->importType,
            'file' => $this->filePath,
            'chunk_size' => $this->chunkSize,
        ]);

        try {
            // Update status to processing
            $this->import->update([
                'status' => 'processing',
                'started_at' => now(),
            ]);

            // Validate file exists
            $fullPath = $this->getFullFilePath();
            if (!file_exists($fullPath)) {
                throw new \RuntimeException("Import file not found: {$fullPath}");
            }

            // Initialize services
            $reader = new ExcelStreamReader($fullPath, $this->chunkSize);
            $service = new ImportService();

            // Get total rows for progress tracking
            $totalRows = $reader->getTotalRows();
            $this->import->update(['total_rows' => $totalRows]);

            Log::info("[Import #{$this->import->id}] Found {$totalRows} rows to process");

            // Preload caches once at the start
            $service->setImport($this->import);
            $service->preloadCaches();

            $processedRows = 0;
            $totalSuccess = 0;
            $totalFailed = 0;
            $allErrors = [];

            // Process chunks using generator (memory-efficient streaming)
            foreach ($reader->streamChunks() as $chunk) {
                $chunkIndex = $chunk['chunkIndex'];
                $rows = $chunk['rows'];

                Log::info("[Import #{$this->import->id}] Processing chunk {$chunkIndex}", [
                    'rows' => count($rows),
                    'start_row' => $chunk['startRow'],
                    'end_row' => $chunk['endRow'],
                ]);

                try {
                    // Process the chunk
                    $result = $service->processChunk($rows, $this->importType);

                    $totalSuccess += $result['success'];
                    $totalFailed += $result['failed'];
                    $processedRows += count($rows);

                    if (!empty($result['errors'])) {
                        $allErrors = array_merge($allErrors, $result['errors']);
                    }

                    // Update progress after each chunk
                    $this->import->update([
                        'processed_rows' => $processedRows,
                        'success_rows' => $totalSuccess,
                        'failed_rows' => $totalFailed,
                        'errors' => array_slice($allErrors, -100, 100, true), // Keep last 100 errors
                    ]);

                    Log::info("[Import #{$this->import->id}] Chunk {$chunkIndex} completed", [
                        'success' => $result['success'],
                        'failed' => $result['failed'],
                        'total_processed' => $processedRows,
                    ]);

                } catch (Throwable $e) {
                    Log::error("[Import #{$this->import->id}] Chunk {$chunkIndex} failed: " . $e->getMessage());

                    if (!$this->skipErrors) {
                        throw $e;
                    }

                    // Continue with next chunk if skip_errors is enabled
                    $processedRows += count($rows);
                    $totalFailed += count($rows);
                }

                // Periodic memory cleanup
                if ($chunkIndex % 10 === 0) {
                    gc_collect_cycles();
                }
            }

            // Clear caches to free memory
            $service->clearCaches();
            gc_collect_cycles();

            // Mark as completed
            $this->import->update([
                'status' => 'completed',
                'completed_at' => now(),
                'processed_rows' => $processedRows,
                'success_rows' => $totalSuccess,
                'failed_rows' => $totalFailed,
                'errors' => array_slice($allErrors, -100, 100, true),
                'summary' => [
                    'total' => $processedRows,
                    'success' => $totalSuccess,
                    'failed' => $totalFailed,
                    'duration_seconds' => now()->diffInSeconds($this->import->started_at),
                ],
            ]);

            Log::info("[Import #{$this->import->id}] Import completed successfully", [
                'total' => $processedRows,
                'success' => $totalSuccess,
                'failed' => $totalFailed,
                'duration' => now()->diffInSeconds($this->import->started_at) . 's',
            ]);

        } catch (Throwable $e) {
            $this->handleFailure($e);
            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        Log::error("[Import #{$this->import->id}] Import job failed permanently", [
            'error' => $exception?->getMessage(),
            'trace' => $exception?->getTraceAsString(),
        ]);

        $this->import->update([
            'status' => 'failed',
            'completed_at' => now(),
            'errors' => array_merge(
                $this->import->errors ?? [],
                ['_fatal' => $exception?->getMessage() ?? 'Unknown error']
            ),
        ]);
    }

    /**
     * Handle failure during processing.
     */
    private function handleFailure(Throwable $e): void
    {
        Log::error("[Import #{$this->import->id}] Import processing failed", [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        // Don't update to failed status if we have retries left
        if ($this->attempts() >= $this->tries) {
            $this->import->update([
                'status' => 'failed',
                'completed_at' => now(),
                'errors' => array_merge(
                    $this->import->errors ?? [],
                    ['_fatal' => $e->getMessage()]
                ),
            ]);
        }
    }

    /**
     * Get the full file path.
     */
    private function getFullFilePath(): string
    {
        // If path is relative, prepend storage path
        if (!str_starts_with($this->filePath, '/')) {
            return Storage::disk('local')->path($this->filePath);
        }

        return $this->filePath;
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'import',
            'import:' . $this->import->id,
            'type:' . $this->importType,
        ];
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function retryAfter(): int
    {
        return $this->backoff * $this->attempts();
    }
}
