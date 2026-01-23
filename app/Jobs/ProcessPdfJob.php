<?php

namespace App\Jobs;

use App\Models\File;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 600;
    public $maxExceptions = 3;

    public function __construct(
        public File $file
    ) {}

    public function handle(): void
    {
        try {
            Log::info("ProcessPdfJob started for file ID: {$this->file->id}");

            $media = $this->file->getFirstMedia('documents');
            if (!$media) {
                throw new \Exception('Media not found for file');
            }

            $pdfPath = $media->getPath();
            if (!file_exists($pdfPath)) {
                throw new \Exception('PDF file not found on disk');
            }

            $pageCount = $this->file->pages_count;

            if (extension_loaded('imagick')) {
                $this->processWithImagick($pdfPath, $pageCount);
            } else {
                Log::warning('Imagick not available, skipping page extraction');
                $this->file->update(['status' => 'completed']);
            }

            Log::info("ProcessPdfJob completed for file ID: {$this->file->id}");

        } catch (\Exception $e) {
            Log::error("ProcessPdfJob failed for file ID: {$this->file->id}: " . $e->getMessage());

            $this->file->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function processWithImagick(string $pdfPath, int $pageCount): void
    {
        DB::beginTransaction();

        try {
            $config = config('pdf');

            // Set memory limit for processing
            ini_set('memory_limit', ($config['memory_limit'] ?? 512) . 'M');
            set_time_limit($config['timeout'] ?? 300);

            $imagick = new \Imagick();
            $resolution = $config['resolution'] ?? 150;
            $imagick->setResolution($resolution, $resolution);

            // Set background color for transparency handling
            $imagick->setBackgroundColor(new \ImagickPixel($config['background_color'] ?? 'white'));
            $imagick->readImage($pdfPath);

            $actualPageCount = $imagick->getNumberImages();

            if ($actualPageCount !== $pageCount) {
                $this->file->update(['pages_count' => $actualPageCount]);
                $pageCount = $actualPageCount;
            }

            for ($pageNumber = 0; $pageNumber < $pageCount; $pageNumber++) {
                $imagick->setIteratorIndex($pageNumber);

                $pageFile = File::create([
                    'parent_id' => $this->file->id,
                    'client_id' => $this->file->client_id,
                    'land_id' => $this->file->land_id,
                    'room_id' => $this->file->room_id,
                    'lane_id' => $this->file->lane_id,
                    'stand_id' => $this->file->stand_id,
                    'rack_id' => $this->file->rack_id,
                    'file_name' => $this->file->file_name . '_page_' . ($pageNumber + 1),
                    'page_number' => $pageNumber + 1,
                    'pages_count' => 1,
                    'status' => 'completed',
                    'uploaded_by' => $this->file->uploaded_by,
                ]);

                $tempPdfPath = storage_path('app/temp/page_' . $this->file->id . '_' . ($pageNumber + 1) . '.pdf');

                if (!is_dir(dirname($tempPdfPath))) {
                    mkdir(dirname($tempPdfPath), 0755, true);
                }

                $singlePage = clone $imagick;
                $singlePage->setIteratorIndex($pageNumber);
                $singlePage->setImageFormat('pdf');
                $singlePage->writeImage($tempPdfPath);
                $singlePage->clear();
                $singlePage->destroy();

                $pageFile->addMedia($tempPdfPath)
                    ->toMediaCollection('documents');

                $imagick->setIteratorIndex($pageNumber);
                $imagick->setImageFormat('jpg');
                $imagick->setImageCompressionQuality(80);
                $imagick->thumbnailImage(300, 0);

                $thumbnailPath = storage_path('app/temp/thumb_' . $this->file->id . '_' . ($pageNumber + 1) . '.jpg');
                $imagick->writeImage($thumbnailPath);

                $pageFile->addMedia($thumbnailPath)
                    ->toMediaCollection('thumbnails');
            }

            $imagick->clear();
            $imagick->destroy();

            // Create sub-files based on items with page ranges
            $this->createSubFilesFromItems();

            $this->file->update(['status' => 'completed']);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function createSubFilesFromItems(): void
    {
        // Get items with page ranges for this file
        $fileItems = $this->file->fileItems()->with('item')->get();

        if ($fileItems->isEmpty()) {
            return;
        }

        foreach ($fileItems as $fileItem) {
            $fromPage = $fileItem->from_page;
            $toPage = $fileItem->to_page;

            // Skip if no from_page specified
            if (!$fromPage) {
                continue;
            }

            // If only from_page is set (no to_page), extract only that single page
            if (!$toPage) {
                $toPage = $fromPage;
            }

            // Ensure from_page is less than or equal to to_page
            if ($fromPage > $toPage) {
                [$fromPage, $toPage] = [$toPage, $fromPage];
            }

            // Validate page range is within file's page count
            if ($fromPage > $this->file->pages_count || $toPage > $this->file->pages_count) {
                Log::warning("Page range {$fromPage}-{$toPage} exceeds file page count {$this->file->pages_count} for file {$this->file->id}");
                continue;
            }

            // Create sub-file for this item
            $subFile = File::create([
                'parent_id' => $this->file->id,
                'client_id' => $this->file->client_id,
                'land_id' => $this->file->land_id,
                'room_id' => $this->file->room_id,
                'lane_id' => $this->file->lane_id,
                'stand_id' => $this->file->stand_id,
                'rack_id' => $this->file->rack_id,
                'file_name' => $this->file->file_name . '_' . $fileItem->item->name . '_p' . $fromPage . '-' . $toPage,
                'original_name' => null,
                'page_number' => null,
                'pages_count' => ($toPage - $fromPage + 1),
                'status' => 'completed',
                'uploaded_by' => $this->file->uploaded_by,
            ]);

            // Link the item to the sub-file
            $subFile->fileItems()->create([
                'item_id' => $fileItem->item_id,
                'from_page' => $fromPage,
                'to_page' => $toPage,
            ]);

            // Merge the pages into a single PDF for this sub-file
            $this->mergePages($subFile, $fromPage, $toPage);
        }
    }

    private function mergePages(File $subFile, int $fromPage, int $toPage): void
    {
        try {
            // Get all page files for this range
            $pages = $this->file->pages()
                ->where('page_number', '>=', $fromPage)
                ->where('page_number', '<=', $toPage)
                ->orderBy('page_number')
                ->get();

            if ($pages->isEmpty()) {
                return;
            }

            // Create merged PDF using Imagick
            $mergedPdf = new \Imagick();

            foreach ($pages as $page) {
                $pageMedia = $page->getFirstMedia('documents');
                if ($pageMedia) {
                    $pagePdfPath = $pageMedia->getPath();
                    if (file_exists($pagePdfPath)) {
                        $mergedPdf->readImage($pagePdfPath);
                    }
                }
            }

            // Save merged PDF
            $tempMergedPath = storage_path('app/temp/merged_' . $subFile->id . '.pdf');
            if (!is_dir(dirname($tempMergedPath))) {
                mkdir(dirname($tempMergedPath), 0755, true);
            }

            $mergedPdf->setImageFormat('pdf');
            $mergedPdf->writeImages($tempMergedPath, true);
            $mergedPdf->clear();
            $mergedPdf->destroy();

            // Attach to sub-file
            $subFile->addMedia($tempMergedPath)->toMediaCollection('documents');

            // Create thumbnail from first page
            $firstPage = $pages->first();
            $firstPageThumb = $firstPage->getFirstMedia('thumbnails');
            if ($firstPageThumb) {
                $thumbPath = $firstPageThumb->getPath();
                if (file_exists($thumbPath)) {
                    $subFile->addMedia($thumbPath)->toMediaCollection('thumbnails');
                }
            }

        } catch (\Exception $e) {
            Log::error("Failed to merge pages for sub-file {$subFile->id}: " . $e->getMessage());
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessPdfJob permanently failed for file ID: {$this->file->id}: " . $exception->getMessage());

        $this->file->update([
            'status' => 'failed',
            'error_message' => 'فشلت معالجة الملف بعد عدة محاولات: ' . $exception->getMessage(),
        ]);
    }
}
