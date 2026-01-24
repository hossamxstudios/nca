<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Import;
use App\Models\Client;
use App\Models\Land;
use App\Models\File;
use App\Models\Governorate;
use App\Models\City;
use App\Models\District;
use App\Models\Zone;
use App\Models\Area;
use App\Models\Room;
use App\Models\Lane;
use App\Models\Stand;
use App\Models\Rack;
use App\Jobs\ProcessImportJob;
use App\Models\ActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImportController extends Controller
{
    public function index()
    {
        $imports = Import::with('user')
            ->orderBy('id', 'desc')
            ->paginate(20);

        // Stats
        $totalImports = Import::count();
        $pendingImports = Import::pending()->count();
        $processingImports = Import::processing()->count();
        $completedImports = Import::completed()->count();
        $failedImports = Import::failed()->count();

        return view('admin.imports.index', compact(
            'imports',
            'totalImports',
            'pendingImports',
            'processingImports',
            'completedImports',
            'failedImports'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:51200', // 50MB max
            'type' => 'required|in:full,clients,lands,geographic,archive',
            'skip_errors' => 'nullable|boolean',
            'update_existing' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Create import record
            $import = Import::create([
                'user_id' => Auth::id(),
                'filename' => $request->file('file')->hashName(),
                'original_filename' => $request->file('file')->getClientOriginalName(),
                'type' => $request->type,
                'status' => 'pending',
                'started_at' => now(),
            ]);

            // Store file using Spatie Media Library
            $import->addMediaFromRequest('file')
                ->toMediaCollection('imports');

            DB::commit();

            // Update status to validating
            $import->update(['status' => 'validating']);

            // Dispatch job to queue
            ProcessImportJob::dispatch(
                $import,
                $request->boolean('skip_errors', true),
                $request->boolean('update_existing', false)
            );

            // Log import activity
            ActivityLogger::make()
                ->action(ActivityLog::ACTION_BULK_IMPORT, ActivityLog::GROUP_IMPORTS)
                ->on($import, $request->file('file')->getClientOriginalName())
                ->description("بدء استيراد ملف: {$request->file('file')->getClientOriginalName()}")
                ->withProperties([
                    'type' => $request->type,
                    'filename' => $request->file('file')->getClientOriginalName(),
                ])
                ->log();

            return redirect()->route('admin.imports.index')
                ->with('success', 'تم رفع الملف بنجاح وجاري معالجته');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import upload failed: ' . $e->getMessage());

            return redirect()->route('admin.imports.index')
                ->with('error', 'فشل رفع الملف: ' . $e->getMessage());
        }
    }

    public function show(Import $import)
    {
        $import->load('user');

        // Format errors for user-friendly display
        $formattedErrors = $this->formatErrorsForDisplay($import->errors);

        return view('admin.imports.show', compact('import', 'formattedErrors'));
    }

    /**
     * Format errors for user-friendly display
     */
    private function formatErrorsForDisplay(?array $errors): array
    {
        if (empty($errors)) {
            return [];
        }

        $formatted = [];

        // Handle 'rows' structure
        $rowErrors = $errors['rows'] ?? $errors;

        foreach ($rowErrors as $key => $error) {
            // Extract sheet name and row number from key like "ابو الهول:Row 35"
            $sheetName = $error['sheet'] ?? 'غير محدد';
            $rowNumber = $error['row_number'] ?? '?';
            $data = $error['data'] ?? [];
            $errorMessage = $error['errors'] ?? 'خطأ غير معروف';

            // Parse technical error to user-friendly message
            $friendlyError = $this->parseErrorMessage($errorMessage);

            // Extract meaningful data
            $formatted[] = [
                'sheet' => $sheetName,
                'row' => $rowNumber,
                'client' => $data['owner_name'] ?? 'غير محدد',
                'land' => $data['land_no'] ?? 'غير محدد',
                'file_name' => $data['file_name_col'] ?? $data['file_name'] ?? 'غير محدد',
                'district' => $data['district'] ?? '',
                'zone' => $data['zone'] ?? '',
                'area' => $data['area'] ?? '',
                'location' => implode(' / ', array_filter([
                    $data['room'] ?? null,
                    $data['lane'] ?? null,
                    $data['stand'] ?? null,
                    $data['rack'] ?? null,
                ])),
                'error' => $friendlyError,
                'error_type' => $this->getErrorType($errorMessage),
            ];
        }

        return $formatted;
    }

    /**
     * Parse technical error message to user-friendly Arabic message
     */
    private function parseErrorMessage(string $error): string
    {
        // Database constraint violations
        if (str_contains($error, 'Column \'file_name\' cannot be null')) {
            return 'اسم الملف مطلوب ولا يمكن أن يكون فارغاً';
        }
        if (str_contains($error, 'Column \'client_id\' cannot be null')) {
            return 'العميل مطلوب';
        }
        if (str_contains($error, 'Column \'land_id\' cannot be null')) {
            return 'رقم القطعة مطلوب';
        }
        if (str_contains($error, 'Duplicate entry')) {
            return 'هذا السجل موجود مسبقاً (تكرار)';
        }
        if (str_contains($error, 'Integrity constraint violation')) {
            return 'خطأ في البيانات - قيمة مطلوبة مفقودة';
        }
        if (str_contains($error, 'Data too long')) {
            return 'النص طويل جداً';
        }

        // Validation errors
        if (str_contains($error, 'required')) {
            return 'حقل مطلوب مفقود';
        }
        if (str_contains($error, 'invalid')) {
            return 'قيمة غير صالحة';
        }

        // Generic fallback - remove SQL details
        if (str_contains($error, 'SQLSTATE')) {
            return 'خطأ في حفظ البيانات';
        }

        return $error;
    }

    /**
     * Get error type for styling
     */
    private function getErrorType(string $error): string
    {
        if (str_contains($error, 'Duplicate')) {
            return 'warning';
        }
        if (str_contains($error, 'cannot be null') || str_contains($error, 'required')) {
            return 'danger';
        }
        return 'danger';
    }

    public function progress(Import $import)
    {
        return response()->json([
            'status' => $import->status,
            'status_badge' => $import->status_badge,
            'total_rows' => $import->total_rows,
            'processed_rows' => $import->processed_rows,
            'success_rows' => $import->success_rows,
            'failed_rows' => $import->failed_rows,
            'progress_percentage' => $import->progress_percentage,
            'completed_at' => $import->completed_at?->format('Y-m-d H:i:s'),
        ]);
    }

    public function destroy(Import $import)
    {
        try {
            // Only allow deletion of completed or failed imports
            if (in_array($import->status, ['pending', 'validating', 'processing'])) {
                return redirect()->route('admin.imports.index')
                    ->with('error', 'لا يمكن حذف استيراد قيد المعالجة');
            }

            // Log delete activity
            ActivityLogger::make()
                ->action(ActivityLog::ACTION_DELETE, ActivityLog::GROUP_IMPORTS)
                ->on($import, $import->original_filename)
                ->description("حذف سجل استيراد: {$import->original_filename}")
                ->log();

            $import->delete();

            return redirect()->route('admin.imports.index')
                ->with('success', 'تم حذف سجل الاستيراد بنجاح');

        } catch (\Exception $e) {
            return redirect()->route('admin.imports.index')
                ->with('error', 'فشل حذف سجل الاستيراد');
        }
    }

    public function downloadTemplate(Request $request)
    {
        $type = $request->get('type', 'archive');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);

        // Set headers based on type
        $headers = $this->getTemplateHeaders($type);

        // Write headers
        $colLetter = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($colLetter . '1', $header);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
            $colLetter++;
        }

        // Style headers
        $headerRange = 'A1:' . $sheet->getHighestColumn() . '1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // Add sample data row
        $sampleData = $this->getSampleData($type);
        $colLetter = 'A';
        foreach ($sampleData as $value) {
            $sheet->setCellValue($colLetter . '2', $value);
            $colLetter++;
        }

        // Create response
        $filename = "import_template_{$type}_" . date('Y-m-d') . '.xlsx';

        // Log download template activity
        ActivityLogger::make()
            ->action(ActivityLog::ACTION_EXPORT, ActivityLog::GROUP_IMPORTS)
            ->description("تحميل قالب استيراد: {$type}")
            ->withProperties(['type' => $type, 'filename' => $filename])
            ->log();

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    private function getTemplateHeaders(string $type): array
    {
        return match ($type) {
            'archive' => [
                'رقم',
                'الملف',
                'المالك',
                'القطعه',
                'الحي',
                'المنطقة',
                'المجاورة',
                'الاوضة',
                'الممر',
                'الاستند',
                'الرف',
            ],
            'full' => [
                'اسم العميل',
                'الرقم القومي',
                'الهاتف',
                'الموبايل',
                'المحافظة',
                'المدينة',
                'الحي',
                'المنطقة',
                'المجاورة',
                'رقم القطعة',
                'رقم الوحدة',
                'ملاحظات',
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
                'المحافظة',
                'المدينة',
                'الحي',
                'المنطقة',
                'المجاورة',
                'رقم القطعة',
                'رقم الوحدة',
            ],
            'geographic' => [
                'المحافظة',
                'المدينة',
                'الحي',
                'المنطقة',
                'المجاورة',
            ],
            default => [],
        };
    }

    private function getSampleData(string $type): array
    {
        return match ($type) {
            'archive' => [
                '1',
                'ملف رقم 1',
                'أحمد محمد',
                'نموذج 5',
                'الحي الأول',
                'المنطقة أ',
                'المجاورة 1',
                'غرفة 1',
                'ممر أ',
                'ستاند 1',
                'رف 1',
            ],
            'full' => [
                'أحمد محمد',
                '12345678901234',
                '0223456789',
                '01012345678',
                'القاهرة',
                'القاهرة الجديدة',
                'الحي الأول',
                'المنطقة أ',
                'المجاورة 1',
                '100',
                '1',
                'ملاحظات',
            ],
            'clients' => [
                'أحمد محمد',
                '12345678901234',
                '0223456789',
                '01012345678',
                'ملاحظات',
            ],
            'lands' => [
                'أحمد محمد',
                'القاهرة',
                'القاهرة الجديدة',
                'الحي الأول',
                'المنطقة أ',
                'المجاورة 1',
                '100',
                '1',
            ],
            'geographic' => [
                'القاهرة',
                'القاهرة الجديدة',
                'الحي الأول',
                'المنطقة أ',
                'المجاورة 1',
            ],
            default => [],
        };
    }

}
