<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessImportJob;
use App\Models\Import;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Controller for handling data imports.
 *
 * This controller is intentionally thin - all heavy processing logic
 * is delegated to the ImportService and ProcessImportJob.
 */
class ImportController extends Controller
{
    /**
     * Display the imports index page.
     */
    public function index(): View
    {
        $imports = Import::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.imports.index', [
            'imports' => $imports,
            'totalImports' => Import::count(),
            'pendingImports' => Import::where('status', 'pending')->count(),
            'processingImports' => Import::whereIn('status', ['validating', 'processing'])->count(),
            'completedImports' => Import::where('status', 'completed')->count(),
            'failedImports' => Import::where('status', 'failed')->count(),
        ]);
    }

    /**
     * Store a new import and dispatch the processing job.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:51200', // 50MB max
            'type' => 'required|in:archive,full,clients,lands,geographic',
        ]);

        try {
            // Store the uploaded file
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('imports', $filename, 'local');

            // Create import record
            $import = Import::create([
                'user_id' => Auth::id(),
                'filename' => $filename,
                'original_filename' => $file->getClientOriginalName(),
                'type' => $request->type,
                'status' => 'pending',
                'total_rows' => 0,
                'processed_rows' => 0,
                'success_rows' => 0,
                'failed_rows' => 0,
                'errors' => [],
            ]);

            // Dispatch the job to the queue
            ProcessImportJob::dispatch(
                $import,
                $path,
                $request->type,
                $request->boolean('skip_errors', true),
                100 // chunk size
            );

            // Log activity
            ActivityLogger::make()
                ->action('import_created', 'imports')
                ->on($import)
                ->withProperties([
                    'filename' => $file->getClientOriginalName(),
                    'type' => $request->type,
                ])
                ->log();

            Log::info("[Import #{$import->id}] Import queued for processing", [
                'file' => $filename,
                'type' => $request->type,
            ]);

            return redirect()
                ->route('admin.imports.index')
                ->with('success', 'تم رفع الملف بنجاح وجاري معالجته في الخلفية');

        } catch (\Throwable $e) {
            Log::error('Import upload failed: ' . $e->getMessage());

            return redirect()
                ->route('admin.imports.index')
                ->with('error', 'فشل في رفع الملف: ' . $e->getMessage());
        }
    }

    /**
     * Display import details.
     */
    public function show(Import $import): View
    {
        $import->load('user');

        // Format errors for display
        $formattedErrors = [];
        if (!empty($import->errors) && is_array($import->errors)) {
            foreach ($import->errors as $row => $error) {
                $formattedErrors[] = [
                    'row' => $row,
                    'client' => '-',
                    'land' => '-',
                    'file' => '-',
                    'location' => '-',
                    'error' => is_string($error) ? $error : json_encode($error),
                ];
            }
        }

        return view('admin.imports.show', [
            'import' => $import,
            'formattedErrors' => $formattedErrors,
        ]);
    }

    /**
     * Get import progress (for AJAX polling).
     */
    public function progress(Import $import): JsonResponse
    {
        return response()->json([
            'id' => $import->id,
            'status' => $import->status,
            'status_badge' => $import->status_badge,
            'progress_percentage' => $import->progress_percentage,
            'total_rows' => $import->total_rows,
            'processed_rows' => $import->processed_rows,
            'success_rows' => $import->success_rows,
            'failed_rows' => $import->failed_rows,
        ]);
    }

    /**
     * Get import details as JSON.
     */
    public function showJson(Import $import): JsonResponse
    {
        return response()->json([
            'success' => true,
            'import' => [
                'id' => $import->id,
                'original_filename' => $import->original_filename,
                'type' => $import->type,
                'type_label' => $import->type_label,
                'status' => $import->status,
                'status_badge' => $import->status_badge,
                'progress_percentage' => $import->progress_percentage,
                'total_rows' => $import->total_rows,
                'processed_rows' => $import->processed_rows,
                'success_rows' => $import->success_rows,
                'failed_rows' => $import->failed_rows,
                'errors' => $import->errors,
                'summary' => $import->summary,
                'user' => $import->user?->name,
                'started_at' => $import->started_at?->format('Y-m-d H:i:s'),
                'completed_at' => $import->completed_at?->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Delete an import record.
     */
    public function destroy(Import $import): RedirectResponse
    {
        try {
            // Only allow deletion of completed or failed imports
            if (!in_array($import->status, ['completed', 'failed'])) {
                return redirect()
                    ->route('admin.imports.index')
                    ->with('error', 'لا يمكن حذف الاستيراد أثناء المعالجة');
            }

            // Delete the uploaded file
            if ($import->filename) {
                Storage::disk('local')->delete('imports/' . $import->filename);
            }

            // Log activity
            ActivityLogger::make()
                ->action('import_deleted', 'imports')
                ->on($import)
                ->withProperties([
                    'filename' => $import->original_filename,
                ])
                ->log();

            $import->delete();

            return redirect()
                ->route('admin.imports.index')
                ->with('success', 'تم حذف سجل الاستيراد بنجاح');

        } catch (\Throwable $e) {
            Log::error('Import deletion failed: ' . $e->getMessage());

            return redirect()
                ->route('admin.imports.index')
                ->with('error', 'فشل في حذف سجل الاستيراد');
        }
    }

    /**
     * Download an Excel template for the specified import type.
     */
    public function downloadTemplate(string $type)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);

        // Set headers based on type
        $headers = $this->getTemplateHeaders($type);

        foreach ($headers as $col => $header) {
            $colLetter = chr(65 + $col); // A, B, C, etc.
            $sheet->setCellValue($colLetter . '1', $header);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Style header row
        $lastCol = chr(65 + count($headers) - 1);
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0'],
            ],
        ]);

        // Add sample data for archive template
        if ($type === 'archive') {
            $sampleData = [
                ['1', 'ملف-001', 'أحمد محمد', '123', 'الحي الأول', 'المنطقة أ', 'المجاورة 1', 'غرفة 1', 'ممر أ', 'ستاند 1', 'رف 1'],
                ['2', 'ملف-002', 'محمد علي', '456', 'الحي الثاني', 'المنطقة ب', 'المجاورة 2', 'غرفة 2', 'ممر ب', 'ستاند 2', 'رف 2'],
            ];

            foreach ($sampleData as $rowIndex => $row) {
                foreach ($row as $col => $value) {
                    $colLetter = chr(65 + $col);
                    $sheet->setCellValue($colLetter . ($rowIndex + 2), $value);
                }
            }
        }

        // Generate filename
        $filename = "template_{$type}_" . date('Y-m-d') . '.xlsx';

        // Return as download
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Get template headers based on import type.
     */
    private function getTemplateHeaders(string $type): array
    {
        return match ($type) {
            'archive' => [
                'رقم',
                'الملف',
                'المالك',
                'القطعه',
                'الحى',
                'المنطقة',
                'المجاورة',
                'الاوضة',
                'الممر',
                'الاستند',
                'الرف',
            ],
            'full' => [
                'رقم',
                'الملف',
                'المالك',
                'الرقم القومي',
                'الهاتف',
                'الموبايل',
                'القطعه',
                'المحافظة',
                'المدينة',
                'الحى',
                'المنطقة',
                'المجاورة',
            ],
            'clients' => [
                'اسم العميل',
                'الرقم القومي',
                'الهاتف',
                'الموبايل',
                'ملاحظات',
            ],
            'lands' => [
                'اسم العميل',
                'رقم القطعة',
                'المحافظة',
                'المدينة',
                'الحي',
                'العنوان',
            ],
            'geographic' => [
                'المحافظة',
                'المدينة',
                'الحي',
                'المنطقة',
                'المجاورة',
            ],
            default => ['رقم', 'البيانات'],
        };
    }
}

