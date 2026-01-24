<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\File;
use App\Models\Governorate;
use App\Models\Item;
use App\Models\Land;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        // Stats
        $totalClients = Client::count();
        $totalFiles = File::whereNull('parent_id')->count();
        $totalPages = File::sum('pages_count');

        // Search parameters
        $searchTerm = $request->input('search');
        $searchField = $request->input('search_field', 'name');
        $barcode = $request->input('barcode');

        // Build query with eager loading
        $query = Client::with([
            'files' => function ($q) {
                $q->whereNull('parent_id')
                  ->with(['land.district', 'land.zone', 'land.area', 'room', 'lane', 'stand', 'rack']);
            },
            'lands'
        ]);

        // Apply search filters
        if ($searchTerm) {
            switch ($searchField) {
                case 'name':
                    $query->where('name', 'like', "%{$searchTerm}%");
                    break;
                case 'mobile':
                    $query->where('mobile', 'like', "%{$searchTerm}%");
                    break;
                case 'telephone':
                    $query->where('telephone', 'like', "%{$searchTerm}%");
                    break;
                case 'national_id':
                    $query->where('national_id', 'like', "%{$searchTerm}%");
                    break;
                case 'file_name':
                    $query->whereHas('files', function ($q) use ($searchTerm) {
                        $q->where('file_name', 'like', "%{$searchTerm}%");
                    });
                    break;
                case 'land_no':
                    $query->whereHas('lands', function ($q) use ($searchTerm) {
                        $q->where('land_no', 'like', "%{$searchTerm}%");
                    });
                    break;
            }
        }

        // Barcode search
        if ($barcode) {
            $query->whereHas('files', function ($q) use ($barcode) {
                $q->where('barcode', $barcode);
            });
        }

        // Role-based visibility: Super Admin sees all, others only see search/barcode results
        $user = $request->user();
        $isSuperAdmin = $user && $user->hasRole('Super Admin');
        $hasSearchCriteria = $searchTerm || $barcode;
        $requiresSearch = !$isSuperAdmin;

        if (!$isSuperAdmin && !$hasSearchCriteria) {
            // Non-Super Admin without search criteria - show empty results with message
            $clients = Client::whereRaw('1 = 0')->paginate(25)->withQueryString();
        } else {
            $clients = $query->orderBy('excel_row_number', 'asc')->paginate(25)->withQueryString();
        }

        $items = Item::orderBy('order')->get();

        return view('admin.clients.index', compact(
            'clients',
            'totalClients',
            'totalFiles',
            'totalPages',
            'searchTerm',
            'searchField',
            'barcode',
            'items',
            'requiresSearch'
        ));
    }

    public function show(Client $client)
    {
        $client->load([
            'files' => function ($q) {
                $q->whereNull('parent_id')
                  ->with(['land.governorate', 'land.city', 'land.district', 'land.zone', 'land.area',
                          'room', 'lane', 'stand', 'rack', 'items', 'children']);
            },
            'lands.governorate', 'lands.city', 'lands.district', 'lands.zone', 'lands.area'
        ]);

        // Log view activity
        ActivityLogger::viewed($client, ActivityLog::GROUP_CLIENTS);

        $items = Item::orderBy('order')->get();

        return view('admin.clients.show', compact('client', 'items'));
    }


    public function store(StoreClientRequest $request)
    {
        DB::beginTransaction();
        try {
            $client = Client::create($request->validated());

            // Log create activity
            ActivityLogger::created($client, ActivityLog::GROUP_CLIENTS, $request->validated());

            DB::commit();
            return redirect()->route('admin.clients.index')
                ->with('success', 'تم إضافة العميل بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating client: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إضافة العميل')->withInput();
        }
    }


    public function update(UpdateClientRequest $request, Client $client)
    {
        DB::beginTransaction();
        try {
            $oldValues = $client->only(['name', 'national_id', 'mobile', 'telephone', 'address']);
            $client->update($request->validated());
            $newValues = $client->only(['name', 'national_id', 'mobile', 'telephone', 'address']);

            // Log update activity with changes
            ActivityLogger::updated($client, ActivityLog::GROUP_CLIENTS, $oldValues, $newValues);

            DB::commit();
            return redirect()->route('admin.clients.index')
                ->with('success', 'تم تحديث بيانات العميل بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating client: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحديث بيانات العميل')->withInput();
        }
    }


    public function destroy(Client $client)
    {
        DB::beginTransaction();
        try {
            // Log delete activity before deleting
            ActivityLogger::deleted($client, ActivityLog::GROUP_CLIENTS);

            $client->delete();
            DB::commit();
            return redirect()->route('admin.clients.index')
                ->with('success', 'تم حذف العميل بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting client: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف العميل');
        }
    }

    public function printBarcodes(Client $client)
    {
        $client->load(['files' => function ($q) {
            $q->whereNull('parent_id')->whereNotNull('barcode');
        }]);

        // Log print activity
        $totalPages = $client->files->sum('pages_count') ?: 1;
        ActivityLogger::printed(
            "طباعة باركود للعميل: {$client->name} ({$totalPages} صفحة)",
            [$client->id],
            ActivityLog::GROUP_CLIENTS
        );

        return view('admin.clients.print-barcodes', compact('client'));
    }

    /**
     * Log print activity for single client barcode (AJAX)
     */
    public function logPrint(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'client_name' => 'required|string',
            'pages_count' => 'required|integer|min:1',
        ]);

        ActivityLogger::printed(
            "طباعة باركود للعميل: {$request->client_name} ({$request->pages_count} صفحة)",
            [$request->client_id],
            ActivityLog::GROUP_CLIENTS
        );

        return response()->json(['success' => true]);
    }

    /**
     * Bulk delete selected clients
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'client_ids' => 'required|array|min:1',
            'client_ids.*' => 'exists:clients,id',
        ]);

        DB::beginTransaction();
        try {
            // Get client names for logging
            $clientNames = Client::whereIn('id', $request->client_ids)->pluck('name')->toArray();

            $count = Client::whereIn('id', $request->client_ids)->delete();

            // Log bulk delete activity
            ActivityLogger::bulkDeleted(
                "حذف {$count} عميل: " . implode('، ', array_slice($clientNames, 0, 5)) . (count($clientNames) > 5 ? '...' : ''),
                $request->client_ids,
                ActivityLog::GROUP_CLIENTS
            );

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "تم حذف {$count} عميل بنجاح"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الحذف'
            ], 500);
        }
    }

    /**
     * Get bulk print data for selected clients
     */
    public function bulkPrintData(Request $request)
    {
        $request->validate([
            'client_ids' => 'required|array|min:1',
            'client_ids.*' => 'exists:clients,id',
        ]);

        $clients = Client::with(['files' => function ($q) {
            $q->whereNull('parent_id')
              ->whereNotNull('barcode')
              ->with(['land.district', 'land.zone', 'land.area']);
        }])->whereIn('id', $request->client_ids)->get();

        $data = [];
        foreach ($clients as $client) {
            foreach ($client->files as $file) {
                if ($file->barcode) {
                    $geo = $file->land
                        ? collect([$file->land->district?->name, $file->land->zone?->name, $file->land->area?->name, $file->land->land_no])
                            ->filter()->implode(' - ')
                        : '-';

                    $data[] = [
                        'client_name' => $client->name,
                        'barcode' => $file->barcode,
                        'file_name' => $file->file_name,
                        'pages_count' => $file->pages_count ?? 1,
                        'geo' => $geo,
                    ];
                }
            }
        }

        // Log bulk print activity
        $totalStickers = collect($data)->sum('pages_count');
        ActivityLogger::printed(
            "طباعة باركود جماعي: {$clients->count()} عميل ({$totalStickers} استيكر)",
            $request->client_ids,
            ActivityLog::GROUP_CLIENTS
        );

        return response()->json([
            'success' => true,
            'data' => $data,
            'total_stickers' => $totalStickers
        ]);
    }

    /**
     * Display trashed clients
     */
    public function trash(Request $request)
    {
        $searchTerm = $request->input('search');

        $query = Client::onlyTrashed()->with(['files' => function ($q) {
            $q->whereNull('parent_id');
        }]);

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('national_id', 'like', "%{$searchTerm}%")
                  ->orWhere('mobile', 'like', "%{$searchTerm}%");
            });
        }

        $clients = $query->orderBy('deleted_at', 'desc')->paginate(25)->withQueryString();
        $totalTrashed = Client::onlyTrashed()->count();

        return view('admin.clients.trash', compact('clients', 'totalTrashed', 'searchTerm'));
    }

    /**
     * Restore a soft-deleted client
     */
    public function restore($id)
    {
        $client = Client::onlyTrashed()->findOrFail($id);
        $client->restore();

        // Log restore activity
        ActivityLogger::make()
            ->action('restore', ActivityLog::GROUP_CLIENTS)
            ->on($client)
            ->description("استعادة العميل: {$client->name}")
            ->log();

        return redirect()->back()->with('success', 'تم استعادة العميل بنجاح');
    }

    /**
     * Force delete a client permanently
     */
    public function forceDelete($id)
    {
        DB::beginTransaction();
        try {
            $client = Client::onlyTrashed()->findOrFail($id);
            $clientName = $client->name;

            // Log force delete activity before deleting
            ActivityLogger::make()
                ->action('force_delete', ActivityLog::GROUP_CLIENTS)
                ->description("حذف نهائي للعميل: {$clientName}")
                ->withProperties(['client_id' => $id, 'client_name' => $clientName])
                ->log();

            $client->forceDelete();
            DB::commit();
            return redirect()->back()->with('success', 'تم حذف العميل نهائياً');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Force delete error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء الحذف');
        }
    }

    /**
     * Bulk force delete selected clients
     */
    public function bulkForceDelete(Request $request)
    {
        $request->validate([
            'client_ids' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Get client names for logging
            $clientNames = Client::onlyTrashed()->whereIn('id', $request->client_ids)->pluck('name')->toArray();

            $count = Client::onlyTrashed()->whereIn('id', $request->client_ids)->forceDelete();

            // Log bulk force delete activity
            ActivityLogger::make()
                ->action('bulk_force_delete', ActivityLog::GROUP_CLIENTS)
                ->description("حذف نهائي لـ {$count} عميل: " . implode('، ', array_slice($clientNames, 0, 5)) . (count($clientNames) > 5 ? '...' : ''))
                ->withAffectedIds($request->client_ids)
                ->batch(null, $count)
                ->log();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "تم حذف {$count} عميل نهائياً"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk force delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الحذف'
            ], 500);
        }
    }

    /**
     * Bulk restore selected clients
     */
    public function bulkRestore(Request $request)
    {
        $request->validate([
            'client_ids' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Get client names for logging
            $clientNames = Client::onlyTrashed()->whereIn('id', $request->client_ids)->pluck('name')->toArray();

            $count = Client::onlyTrashed()->whereIn('id', $request->client_ids)->restore();

            // Log bulk restore activity
            ActivityLogger::make()
                ->action('bulk_restore', ActivityLog::GROUP_CLIENTS)
                ->description("استعادة {$count} عميل: " . implode('، ', array_slice($clientNames, 0, 5)) . (count($clientNames) > 5 ? '...' : ''))
                ->withAffectedIds($request->client_ids)
                ->batch(null, $count)
                ->log();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "تم استعادة {$count} عميل بنجاح"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk restore error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الاستعادة'
            ], 500);
        }
    }

    /**
     * Export clients to Excel in the same format as import
     * Headers: رقم, الملف, المالك, القطعه, الحي, المنطقة, المجاورة, الاوضة, الممر, الاستند, الرف
     */
    public function export(Request $request)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setRightToLeft(true);

            // Headers matching import format
            $headers = ['رقم', 'الملف', 'المالك', 'القطعه', 'الحي', 'المنطقة', 'المجاورة', 'الاوضة', 'الممر', 'الاستند', 'الرف'];

            // Set headers
            foreach ($headers as $colIndex => $header) {
                $col = chr(65 + $colIndex); // A, B, C...
                $sheet->setCellValue($col . '1', $header);
            }

            // Style headers
            $headerRange = 'A1:K1';
            $sheet->getStyle($headerRange)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ]);

            // Get all clients with their files
            $clients = Client::with([
                'files' => function ($q) {
                    $q->whereNull('parent_id')
                      ->with(['land.district', 'land.zone', 'land.area', 'room', 'lane', 'stand', 'rack']);
                }
            ])->orderBy('id')->get();

            $rowNum = 2;
            $fileNumber = 1;

            foreach ($clients as $client) {
                $clientName = $client->name ?: 'لا يوجد اسم';

                // If client has files, create one row per file
                if ($client->files->count() > 0) {
                    foreach ($client->files as $file) {
                        $sheet->setCellValue('A' . $rowNum, $fileNumber);
                        $sheet->setCellValue('B' . $rowNum, $file->file_name ?: 'لا يوجد');
                        $sheet->setCellValue('C' . $rowNum, $clientName);
                        $sheet->setCellValue('D' . $rowNum, $file->land?->land_no ?? '');
                        $sheet->setCellValue('E' . $rowNum, $file->land?->district?->name ?? '');
                        $sheet->setCellValue('F' . $rowNum, $file->land?->zone?->name ?? '');
                        $sheet->setCellValue('G' . $rowNum, $file->land?->area?->name ?? '');
                        $sheet->setCellValue('H' . $rowNum, $file->room?->name ?? '');
                        $sheet->setCellValue('I' . $rowNum, $file->lane?->name ?? '');
                        $sheet->setCellValue('J' . $rowNum, $file->stand?->name ?? '');
                        $sheet->setCellValue('K' . $rowNum, $file->rack?->name ?? '');

                        $rowNum++;
                        $fileNumber++;
                    }
                } else {
                    // Client has no files - still export with empty file info
                    $sheet->setCellValue('A' . $rowNum, $fileNumber);
                    $sheet->setCellValue('B' . $rowNum, 'لا يوجد');
                    $sheet->setCellValue('C' . $rowNum, $clientName);
                    $sheet->setCellValue('D' . $rowNum, '');
                    $sheet->setCellValue('E' . $rowNum, '');
                    $sheet->setCellValue('F' . $rowNum, '');
                    $sheet->setCellValue('G' . $rowNum, '');
                    $sheet->setCellValue('H' . $rowNum, '');
                    $sheet->setCellValue('I' . $rowNum, '');
                    $sheet->setCellValue('J' . $rowNum, '');
                    $sheet->setCellValue('K' . $rowNum, '');

                    $rowNum++;
                    $fileNumber++;
                }
            }

            // Style data rows
            if ($rowNum > 2) {
                $dataRange = 'A2:K' . ($rowNum - 1);
                $sheet->getStyle($dataRange)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);
            }

            // Auto-size columns
            foreach (range('A', 'K') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Set sheet title
            $sheet->setTitle('العملاء والملفات');

            // Generate filename with date
            $filename = 'clients_export_' . date('Y-m-d_His') . '.xlsx';

            // Log export activity
            ActivityLogger::make()
                ->action(ActivityLog::ACTION_EXPORT, ActivityLog::GROUP_CLIENTS)
                ->description("تصدير بيانات العملاء ({$clients->count()} عميل)")
                ->withProperties(['total_clients' => $clients->count(), 'filename' => $filename])
                ->log();

            // Create response
            $writer = new Xlsx($spreadsheet);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تصدير البيانات: ' . $e->getMessage());
        }
    }
}
