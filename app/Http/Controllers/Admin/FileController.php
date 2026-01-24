<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\File\StoreFileRequest;
use App\Models\File;
use App\Models\FileItem;
use App\Models\Client;
use App\Models\Land;
use App\Models\Room;
use App\Models\Lane;
use App\Models\Stand;
use App\Models\Rack;
use App\Models\District;
use App\Models\Zone;
use App\Models\Area;
use App\Models\Item;
use App\Jobs\ProcessPdfJob;
use App\Models\ActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use Smalot\PdfParser\Parser as PdfParser;

class FileController extends Controller {

    public function upload(Request $request, File $file)
    {
        $request->validate([
            'pdf_file' => 'required|file|mimes:pdf|max:102400',
            'items' => 'nullable|array',
            'items.*.enabled' => 'nullable',
            'items.*.from_page' => 'nullable|integer|min:1',
            'items.*.to_page' => 'nullable|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Upload PDF to media library
            $media = $file->addMediaFromRequest('pdf_file')->toMediaCollection('files');

            // Get total pages from PDF
            $pdfPath = $media->getPath();
            $totalPages = $this->getPdfPageCount($pdfPath);

            // Update file pages_count
            $file->update(['pages_count' => $totalPages]);

            // Save item page ranges (no extraction - preview will use original PDF)
            if ($request->has('items')) {
                foreach ($request->items as $itemId => $itemData) {
                    if (isset($itemData['enabled']) && isset($itemData['from_page']) && isset($itemData['to_page'])) {
                        FileItem::create([
                            'file_id' => $file->id,
                            'item_id' => $itemId,
                            'from_page' => $itemData['from_page'],
                            'to_page' => $itemData['to_page'],
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'تم رفع الملف بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('File upload error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء رفع الملف: ' . $e->getMessage());
        }
    }

    /**
     * Download specific pages from a file's PDF
     */
    public function downloadPages(Request $request, File $file)
    {
        $request->validate([
            'from_page' => 'required|integer|min:1',
            'to_page' => 'required|integer|min:1',
            'filename' => 'nullable|string|max:255',
        ]);

        $fromPage = (int) $request->from_page;
        $toPage = (int) $request->to_page;
        $filename = $request->filename ?? 'document';

        // Get original PDF
        $media = $file->getFirstMedia('files');
        if (!$media) {
            return response()->json(['error' => 'لا يوجد ملف PDF'], 404);
        }

        $pdfPath = $media->getPath();

        // Create temp directory
        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $outputPath = $tempDir . '/download_' . $file->id . '_' . time() . '.pdf';
        $pageRange = $fromPage === $toPage ? $fromPage : "{$fromPage}-{$toPage}";

        // Extract pages using FPDI (pure PHP - no external dependencies)
        try {
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($pdfPath);

            // Validate page range
            if ($fromPage > $pageCount || $toPage > $pageCount) {
                throw new \Exception("نطاق الصفحات غير صالح. الملف يحتوي على {$pageCount} صفحة فقط.");
            }

            // Import selected pages
            for ($pageNo = $fromPage; $pageNo <= $toPage; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);
            }

            // Save to output file
            $pdf->Output($outputPath, 'F');

            if (!file_exists($outputPath)) {
                throw new \Exception('فشل استخراج الصفحات');
            }

            // Log download activity
            $pagesCount = $toPage - $fromPage + 1;
            $client = $file->client;
            ActivityLogger::make()
                ->action(ActivityLog::ACTION_DOWNLOAD, ActivityLog::GROUP_FILES)
                ->description("تحميل صفحات: (ص {$fromPage} - {$toPage}) - ملف: {$file->file_name} - عميل: {$client->name}")
                ->withNewValues([
                    'client_id' => $client->id,
                    'client_name' => $client->name,
                    'file_id' => $file->id,
                    'file_name' => $file->file_name,
                    'from_page' => $fromPage,
                    'to_page' => $toPage,
                    'pages_count' => $pagesCount,
                ])
                ->log();

            // Return file for download and delete after
            return response()->download($outputPath, "{$filename}.pdf")->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('PDF extraction error: ' . $e->getMessage());

            // Cleanup
            if (file_exists($outputPath)) {
                unlink($outputPath);
            }

            return response()->json(['error' => 'حدث خطأ أثناء استخراج الصفحات: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update file items (add/remove/modify page ranges)
     */
    public function updateItems(Request $request, File $file)
    {
        $request->validate([
            'items' => 'nullable|array',
            'items.*.enabled' => 'nullable',
            'items.*.from_page' => 'nullable|integer|min:1',
            'items.*.to_page' => 'nullable|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Delete all existing file items
            FileItem::where('file_id', $file->id)->delete();

            // Save new item page ranges
            if ($request->has('items')) {
                foreach ($request->items as $itemId => $itemData) {
                    if (isset($itemData['enabled']) && isset($itemData['from_page']) && isset($itemData['to_page'])) {
                        FileItem::create([
                            'file_id' => $file->id,
                            'item_id' => $itemId,
                            'from_page' => $itemData['from_page'],
                            'to_page' => $itemData['to_page'],
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'تم تحديث الملفات الفرعية بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('File items update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء التحديث: ' . $e->getMessage());
        }
    }

    /**
     * Get PDF page count using PdfParser (pure PHP)
     */
    private function getPdfPageCount(string $pdfPath): int
    {
        try {
            // Use PdfParser - pure PHP, no external dependencies
            $parser = new PdfParser();
            $pdf = $parser->parseFile($pdfPath);
            $pages = $pdf->getPages();
            return count($pages);
        } catch (\Exception $e) {
            Log::warning('PdfParser failed: ' . $e->getMessage());
        }

        // Fallback: count pages by reading PDF structure
        try {
            $content = file_get_contents($pdfPath);
            $count = preg_match_all("/\/Page\W/", $content, $matches);
            return max(1, $count);
        } catch (\Exception $e) {
            return 1;
        }
    }

    /**
     * Store a new file for a client with PDF upload and items
     */
    public function store(Request $request, Client $client)
    {
        $request->validate([
            'file_name' => 'required|string|max:255',
            'pdf_file' => 'required|file|mimes:pdf|max:102400',
            'district_id' => 'required|exists:districts,id',
            'zone_id' => 'required|exists:zones,id',
            'area_id' => 'nullable|exists:areas,id',
            'land_no' => 'required|string|max:255',
            'room_id' => 'nullable|exists:rooms,id',
            'lane_id' => 'nullable|exists:lanes,id',
            'stand_id' => 'nullable|exists:stands,id',
            'rack_id' => 'nullable|exists:racks,id',
            'items' => 'nullable|array',
            'items.*.enabled' => 'nullable',
            'items.*.from_page' => 'nullable|integer|min:1',
            'items.*.to_page' => 'nullable|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Create Land
            $land = Land::create([
                'client_id' => $client->id,
                'district_id' => $request->district_id,
                'zone_id' => $request->zone_id,
                'area_id' => $request->area_id,
                'land_no' => $request->land_no,
            ]);

            // Create File
            $file = File::create([
                'client_id' => $client->id,
                'land_id' => $land->id,
                'file_name' => $request->file_name,
                'barcode' => File::generateBarcode(),
                'room_id' => $request->room_id,
                'lane_id' => $request->lane_id,
                'stand_id' => $request->stand_id,
                'rack_id' => $request->rack_id,
                'status' => 'pending',
            ]);

            // Upload PDF to media library
            $media = $file->addMediaFromRequest('pdf_file')->toMediaCollection('files');

            // Get total pages from PDF
            $pdfPath = $media->getPath();
            $totalPages = $this->getPdfPageCount($pdfPath);

            // Update file pages_count
            $file->update(['pages_count' => $totalPages, 'status' => 'completed']);

            // Save item page ranges
            if ($request->has('items')) {
                foreach ($request->items as $itemId => $itemData) {
                    if (isset($itemData['enabled']) && isset($itemData['from_page']) && isset($itemData['to_page'])) {
                        FileItem::create([
                            'file_id' => $file->id,
                            'item_id' => $itemId,
                            'from_page' => $itemData['from_page'],
                            'to_page' => $itemData['to_page'],
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'تم إضافة الملف ورفعه بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('File creation error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة الملف: ' . $e->getMessage());
        }
    }

    /**
     * Download the full original PDF file
     */
    public function downloadOriginal(File $file)
    {
        $media = $file->getFirstMedia('files');
        if (!$media) {
            return back()->with('error', 'لا يوجد ملف PDF');
        }

        // Load relationships for filename
        $file->load(['client', 'land.district', 'land.zone', 'land.area', 'room', 'lane', 'stand', 'rack']);
        $client = $file->client;

        // Build descriptive filename
        // Format: ClientName_District-Zone-Area-LandNo_Room-Lane-Stand-Rack_FileNumber.pdf
        $geoLocation = collect([
            $file->land?->district?->name,
            $file->land?->zone?->name,
            $file->land?->area?->name,
            $file->land?->land_no ? "أرض{$file->land->land_no}" : null,
        ])->filter()->implode('-');

        $physicalLocation = collect([
            $file->room?->name ? "غ{$file->room->name}" : null,
            $file->lane?->name ? "م{$file->lane->name}" : null,
            $file->stand?->name ? "س{$file->stand->name}" : null,
            $file->rack?->name ? "ر{$file->rack->name}" : null,
        ])->filter()->implode('-');

        $filenameParts = collect([
            $client->name,
            $geoLocation ?: null,
            $physicalLocation ?: null,
            $file->file_name,
        ])->filter()->implode('_');

        // Sanitize filename (remove invalid characters)
        $filename = preg_replace('/[\/\\\\:*?"<>|]/', '-', $filenameParts);

        // Log download activity
        ActivityLogger::make()
            ->action(ActivityLog::ACTION_DOWNLOAD, ActivityLog::GROUP_FILES)
            ->description("تحميل الملف الأصلي: {$file->file_name} ({$file->pages_count} صفحة) - عميل: {$client->name}")
            ->withNewValues([
                'client_id' => $client->id,
                'client_name' => $client->name,
                'file_id' => $file->id,
                'file_name' => $file->file_name,
                'pages_count' => $file->pages_count,
            ])
            ->log();

        return response()->download($media->getPath(), "{$filename}.pdf");
    }

    /**
     * Update file location (geo + physical) - Super Admin only
     */
    public function updateLocation(Request $request, File $file)
    {
        $request->validate([
            'district_id' => 'required|exists:districts,id',
            'zone_id' => 'required|exists:zones,id',
            'area_id' => 'nullable|exists:areas,id',
            'land_no' => 'required|string|max:255',
            'room_id' => 'nullable|exists:rooms,id',
            'lane_id' => 'nullable|exists:lanes,id',
            'stand_id' => 'nullable|exists:stands,id',
            'rack_id' => 'nullable|exists:racks,id',
        ]);

        // Capture old values for logging
        $oldGeoValues = $file->land ? [
            'district' => $file->land->district?->name,
            'zone' => $file->land->zone?->name,
            'area' => $file->land->area?->name,
            'land_no' => $file->land->land_no,
        ] : [];
        $oldPhysicalValues = [
            'room' => $file->room?->name,
            'lane' => $file->lane?->name,
            'stand' => $file->stand?->name,
            'rack' => $file->rack?->name,
        ];

        DB::beginTransaction();
        try {
            // Update or create Land
            if ($file->land) {
                $file->land->update([
                    'district_id' => $request->district_id,
                    'zone_id' => $request->zone_id,
                    'area_id' => $request->area_id,
                    'land_no' => $request->land_no,
                ]);
            } else {
                $land = Land::create([
                    'client_id' => $file->client_id,
                    'district_id' => $request->district_id,
                    'zone_id' => $request->zone_id,
                    'area_id' => $request->area_id,
                    'land_no' => $request->land_no,
                ]);
                $file->land_id = $land->id;
            }

            // Update physical location
            $file->update([
                'room_id' => $request->room_id,
                'lane_id' => $request->lane_id,
                'stand_id' => $request->stand_id,
                'rack_id' => $request->rack_id,
            ]);

            // Refresh relations for new values
            $file->refresh();
            $file->load(['land.district', 'land.zone', 'land.area', 'room', 'lane', 'stand', 'rack']);

            // Get new values for logging
            $newGeoValues = [
                'district' => $file->land?->district?->name,
                'zone' => $file->land?->zone?->name,
                'area' => $file->land?->area?->name,
                'land_no' => $file->land?->land_no,
            ];
            $newPhysicalValues = [
                'room' => $file->room?->name,
                'lane' => $file->lane?->name,
                'stand' => $file->stand?->name,
                'rack' => $file->rack?->name,
            ];

            // Log location update
            $client = $file->client;
            ActivityLogger::make()
                ->action(ActivityLog::ACTION_UPDATE, ActivityLog::GROUP_FILES)
                ->on($file)
                ->description("تحديث موقع ملف: {$file->file_name} - عميل: {$client->name}")
                ->withChanges(
                    ['geo' => $oldGeoValues, 'physical' => $oldPhysicalValues],
                    ['geo' => $newGeoValues, 'physical' => $newPhysicalValues]
                )
                ->log();

            DB::commit();

            return redirect()->back()->with('success', 'تم تحديث موقع الملف بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('File location update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث الموقع: ' . $e->getMessage());
        }
    }

    /**
     * Get all districts
     */
    public function getDistricts()
    {
        $districts = District::orderBy('name')->get(['id', 'name']);
        return response()->json($districts);
    }

    /**
     * Get zones by district
     */
    public function getZones(District $district)
    {
        $zones = $district->zones()->orderBy('name')->get(['id', 'name']);
        return response()->json($zones);
    }

    /**
     * Get areas by zone
     */
    public function getAreas(Zone $zone)
    {
        $areas = $zone->areas()->orderBy('name')->get(['id', 'name']);
        return response()->json($areas);
    }

    /**
     * Get all rooms
     */
    public function getRooms()
    {
        $rooms = Room::orderBy('building_name')->orderBy('name')->get(['id', 'name', 'building_name']);
        return response()->json($rooms);
    }

    /**
     * Get lanes by room
     */
    public function getLanes(Room $room)
    {
        $lanes = $room->lanes()->orderBy('name')->get(['id', 'name']);
        return response()->json($lanes);
    }

    /**
     * Get stands by lane
     */
    public function getStands(Lane $lane)
    {
        $stands = $lane->stands()->orderBy('name')->get(['id', 'name']);
        return response()->json($stands);
    }

    /**
     * Get racks by stand
     */
    public function getRacks(Stand $stand)
    {
        $racks = $stand->racks()->orderBy('name')->get(['id', 'name']);
        return response()->json($racks);
    }
}
