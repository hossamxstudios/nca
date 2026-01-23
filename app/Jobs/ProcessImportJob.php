<?php

namespace App\Jobs;

use App\Models\Import;
use App\Models\Client;
use App\Models\Land;
use App\Models\Governorate;
use App\Models\City;
use App\Models\District;
use App\Models\Zone;
use App\Models\Area;
use App\Models\Room;
use App\Models\Lane;
use App\Models\Stand;
use App\Models\Rack;
use App\Models\File;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 3600;

    public $currentRowNumber = 0;
    public $currentSheetName = '';
    public $lastKnownOwnerName = null; // Track owner name for merged cells
    public $lastKnownFileName = null; // Track file name for merged cells
    public $lastKnownFileNumber = null; // Track file number for merged cells

    public function __construct(
        public Import $import,
        public bool $skipErrors = true,
        public bool $updateExisting = false
    ) {}

    public function handle(): void
    {
        try {
            // Log::info("ProcessImportJob started for import ID: {$this->import->id}");

            $media = $this->import->getFirstMedia('imports');
            if (!$media) {
                throw new \Exception('Import file not found');
            }

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($media->getPath());

            // Process all sheets in the workbook
            $allSheets = $spreadsheet->getAllSheets();
            $totalSheets = count($allSheets);

            Log::info("Found {$totalSheets} sheets in the Excel file");

            $totalRows = 0;
            $successCount = 0;
            $failedCount = 0;
            $errors = [];

            // First pass: count total rows across all sheets
            foreach ($allSheets as $sheet) {
                $rows = $sheet->toArray();
                if (count($rows) > 1) {
                    $totalRows += count($rows) - 1; // Exclude header row
                }
            }

            $this->import->update(['total_rows' => $totalRows]);

            DB::beginTransaction();

            try {
                $processedRows = 0;

                // Process each sheet
                foreach ($allSheets as $sheetIndex => $worksheet) {
                    $sheetName = $worksheet->getTitle();
                    Log::info("Processing sheet: {$sheetName}");

                    // Reset last known values for each sheet
                    $this->lastKnownOwnerName = null;
                    $this->lastKnownFileName = null;
                    $this->lastKnownFileNumber = null;

                    $rows = $worksheet->toArray();

                    if (count($rows) < 2) {
                        Log::info("Sheet {$sheetName} has no data rows, skipping");
                        continue;
                    }

                    $rawHeaders = array_map(fn($h) => trim($h ?? ''), $rows[0]);
                    $headers = $this->mapArabicHeaders($rawHeaders);
                    $dataRows = array_slice($rows, 1);

                    Log::debug("Import headers mapping", [
                        'raw_headers' => $rawHeaders,
                        'mapped_headers' => $headers,
                    ]);

                    foreach ($dataRows as $index => $row) {
                        $rowNumber = $index + 2;
                        $this->currentRowNumber = $rowNumber;
                        $this->currentSheetName = $sheetName;
                        $rowData = array_combine($headers, $row);

                        // Add raw headers for reference (to extract owner name from header)
                        $rowData['_raw_headers'] = $rawHeaders;
                        $rowData['_headers_map'] = array_combine($headers, $rawHeaders);

                        try {
                            $this->processRow($rowData);
                            $successCount++;
                        } catch (\Exception $e) {
                            $failedCount++;
                            $errorKey = "{$sheetName}:Row {$rowNumber}";
                            $errors['rows'][$errorKey] = [
                                'sheet' => $sheetName,
                                'row_number' => $rowNumber,
                                'errors' => $e->getMessage(),
                                'data' => $rowData,
                            ];

                            // Log detailed error information
                            Log::error("Import row failed", [
                                'import_id' => $this->import->id,
                                'sheet' => $sheetName,
                                'row_number' => $rowNumber,
                                'error' => $e->getMessage(),
                                'row_data' => $rowData,
                                'trace' => $e->getTraceAsString(),
                            ]);

                            if (!$this->skipErrors) {
                                throw $e;
                            }
                        }

                        $processedRows++;

                        // Update progress every 10 rows
                        if ($processedRows % 10 === 0) {
                            $this->import->update([
                                'processed_rows' => $processedRows,
                                'success_rows' => $successCount,
                                'failed_rows' => $failedCount,
                            ]);
                        }
                    }

                    Log::info("Completed sheet {$sheetName}: {$successCount} success, {$failedCount} failed");
                }

                DB::commit();

                // Final update with accurate counts
                $this->import->update([
                    'processed_rows' => $processedRows,
                    'success_rows' => $successCount,
                    'failed_rows' => $failedCount,
                ]);

                $this->import->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'errors' => $errors,
                    'summary' => [
                        'total' => $totalRows,
                        'success' => $successCount,
                        'failed' => $failedCount,
                    ],
                ]);

                Log::info("ProcessImportJob completed for import ID: {$this->import->id}");

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error("ProcessImportJob failed for import ID: {$this->import->id}: " . $e->getMessage());

            $this->import->update([
                'status' => 'failed',
                'completed_at' => now(),
                'errors' => ['general' => $e->getMessage()],
            ]);
        }
    }

    private function processRow(array $row): void
    {
        switch ($this->import->type) {
            case 'full':
                $this->processFullRow($row);
                break;
            case 'clients':
                $this->processClientRow($row);
                break;
            case 'lands':
                $this->processLandRow($row);
                break;
            case 'geographic':
                $this->processGeographicRow($row);
                break;
            case 'archive':
                $this->processArchiveRow($row);
                break;
        }
    }

    private function processFullRow(array $row): void
    {
        $governorate = $this->findOrCreateGovernorate($row['governorate'] ?? null);
        $city = $this->findOrCreateCity($row['city'] ?? null, $governorate);
        $district = $this->findOrCreateDistrict($row['district'] ?? null, $city);
        $zone = $this->findOrCreateZone($row['zone'] ?? null, $district);
        $area = $this->findOrCreateArea($row['area'] ?? null, $zone);

        $clientData = [
            'name' => $row['client_name'],
            'national_id' => $row['national_id'] ?? null,
            'client_code' => $row['client_code'] ?? null,
            'telephone' => $row['telephone'] ?? null,
            'mobile' => $row['mobile'] ?? null,
            'notes' => $row['notes'] ?? null,
            'excel_row_number' => $this->currentRowNumber,
        ];

        if ($this->updateExisting && !empty($row['national_id'])) {
            $client = Client::updateOrCreate(
                ['national_id' => $row['national_id']],
                $clientData
            );
        } else {
            $client = Client::create($clientData);
        }

        Land::create([
            'client_id' => $client->id,
            'governorate_id' => $governorate?->id,
            'city_id' => $city?->id,
            'district_id' => $district?->id,
            'zone_id' => $zone?->id,
            'area_id' => $area?->id,
            'land_no' => $row['land_no'],
            'unit_no' => $row['unit_no'] ?? null,
        ]);
    }

    private function processClientRow(array $row): void
    {
        $clientData = [
            'name' => $row['client_name'],
            'national_id' => $row['national_id'] ?? null,
            'client_code' => $row['client_code'] ?? null,
            'telephone' => $row['telephone'] ?? null,
            'mobile' => $row['mobile'] ?? null,
            'notes' => $row['notes'] ?? null,
            'excel_row_number' => $this->currentRowNumber,
        ];

        if ($this->updateExisting && !empty($row['national_id'])) {
            Client::updateOrCreate(
                ['national_id' => $row['national_id']],
                $clientData
            );
        } else {
            Client::create($clientData);
        }
    }

    private function processLandRow(array $row): void
    {
        $client = Client::where('name', $row['client_name'])->first();
        if (!$client) {
            $client = Client::create([
                'name' => $row['client_name'],
                'excel_row_number' => $this->currentRowNumber,
            ]);
        }

        $governorate = $this->findOrCreateGovernorate($row['governorate'] ?? null);
        $city = $this->findOrCreateCity($row['city'] ?? null, $governorate);
        $district = $this->findOrCreateDistrict($row['district'] ?? null, $city);
        $zone = $this->findOrCreateZone($row['zone'] ?? null, $district);
        $area = $this->findOrCreateArea($row['area'] ?? null, $zone);

        Land::create([
            'client_id' => $client->id,
            'governorate_id' => $governorate?->id,
            'city_id' => $city?->id,
            'district_id' => $district?->id,
            'zone_id' => $zone?->id,
            'area_id' => $area?->id,
            'land_no' => $row['land_no'],
            'unit_no' => $row['unit_no'] ?? null,
        ]);
    }

    private function processGeographicRow(array $row): void
    {
        $governorate = $this->findOrCreateGovernorate($row['governorate'] ?? null);
        $city = $this->findOrCreateCity($row['city'] ?? null, $governorate);
        $district = $this->findOrCreateDistrict($row['district'] ?? null, $city);
        $zone = $this->findOrCreateZone($row['zone'] ?? null, $district);
        $this->findOrCreateArea($row['area'] ?? null, $zone);
    }

    private function findOrCreateGovernorate(?string $name): ?Governorate
    {
        if (empty($name)) return null;
        return Governorate::firstOrCreate(['name' => trim($name)]);
    }

    private function findOrCreateCity(?string $name, ?Governorate $governorate): ?City
    {
        if (empty($name) || !$governorate) return null;
        return City::firstOrCreate([
            'governorate_id' => $governorate->id,
            'name' => trim($name),
        ]);
    }

    private function findOrCreateDistrict(?string $name, ?City $city): ?District
    {
        if (empty($name) || !$city) return null;
        return District::firstOrCreate([
            'city_id' => $city->id,
            'name' => trim($name),
        ]);
    }

    private function findOrCreateZone(?string $name, ?District $district): ?Zone
    {
        if (empty($name) || !$district) return null;
        return Zone::firstOrCreate([
            'district_id' => $district->id,
            'name' => trim($name),
        ]);
    }

    private function findOrCreateArea(?string $name, ?Zone $zone): ?Area
    {
        if (empty($name) || !$zone) return null;
        return Area::firstOrCreate([
            'zone_id' => $zone->id,
            'name' => trim($name),
        ]);
    }

    /**
     * Process archive row - handles the Arabic headers format
     * New format: رقم, كود, الاسم, القطعة, المنطقة, الحى, المحافظة, الوظيفة, العمر, الأسرة, الدور
     * Old format: رقم الملف, المالك, القطعه, الحي, المنطقة, المجاورة, الاوضه, الممر, الاستند, الرف
     */
    private function processArchiveRow(array $row): void
    {
        // Log raw row data for debugging
        Log::debug("Processing archive row", ['row_data' => $row]);

        // Map keys - headers are now mapped by mapArabicHeaders()
        $fileNumber = $row['file_number'] ?? null;
        $fileNameCol = $row['file_name_col'] ?? null; // الملف column = file name
        $ownerName = $row['owner_name'] ?? null;
        $landNo = $row['land_no'] ?? null;
        $zoneName = $row['zone'] ?? null;
        $districtName = $row['district'] ?? null;
        $governorateName = $row['governorate'] ?? 'القاهرة';
        $areaName = $row['area'] ?? null;
        $roomName = $row['room'] ?? null;
        $laneName = $row['lane'] ?? null;
        $standName = $row['stand'] ?? null;
        $rackName = $row['rack'] ?? null;
        // Additional fields
        $job = $row['job'] ?? null;
        $age = $row['age'] ?? null;
        $family = $row['family'] ?? null;
        $floor = $row['floor'] ?? null;

        // If owner_name is not in row data, try to extract it from column headers
        // This handles cases where the client name is the column header itself
        if (empty($ownerName) && isset($row['_headers_map'])) {
            $headersMap = $row['_headers_map'];

            // If owner_name column has data, the header itself is the owner name
            if (isset($headersMap['owner_name']) && !empty($row['owner_name'])) {
                $ownerName = $headersMap['owner_name'];
                // The value in this column is actually the land number
                if (empty($landNo)) {
                    $landNo = $row['owner_name'];
                }
            }
        }

        // Handle merged cells: if owner_name is still empty, use last known owner name
        if (empty($ownerName) && !empty($this->lastKnownOwnerName)) {
            $ownerName = $this->lastKnownOwnerName;
            Log::debug("Using last known owner name for merged cell", [
                'row_number' => $this->currentRowNumber,
                'owner_name' => $ownerName,
            ]);
        }

        // If owner name is still empty, use default name
        if (empty($ownerName) || trim($ownerName) === '') {
            $ownerName = 'لا يوجد اسم';
        }

        // Update last known owner name for next row (in case of merged cells)
        $this->lastKnownOwnerName = $ownerName;

        // Handle merged cells for file name and file number
        if (empty($fileNameCol) && !empty($this->lastKnownFileName)) {
            $fileNameCol = $this->lastKnownFileName;
            Log::debug("Using last known file name for merged cell", [
                'row_number' => $this->currentRowNumber,
                'file_name' => $fileNameCol,
            ]);
        }

        if (empty($fileNumber) && !empty($this->lastKnownFileNumber)) {
            $fileNumber = $this->lastKnownFileNumber;
        }

        // Update last known file values for next row (in case of merged cells)
        if (!empty($fileNameCol)) {
            $this->lastKnownFileName = $fileNameCol;
        }
        if (!empty($fileNumber)) {
            $this->lastKnownFileNumber = $fileNumber;
        }

        // Find or create client
        $clientData = [
            'name' => trim($ownerName),
            'excel_row_number' => $this->currentRowNumber,
        ];

        $client = Client::firstOrCreate(
            ['name' => trim($ownerName)],
            $clientData
        );

        // Update client files_code if file number provided
        if (!empty($fileNumber)) {
            $filesCodes = $client->files_code ?? [];
            if (!in_array($fileNumber, $filesCodes)) {
                $filesCodes[] = $fileNumber;
                $client->update(['files_code' => $filesCodes]);
            }
        }

        // Get governorate - use provided or default to القاهرة
        $governorate = Governorate::firstOrCreate(['name' => trim($governorateName ?? 'القاهرة')]);

        // Get default city (القاهرة الجديدة) - can be expanded later
        $city = City::firstOrCreate([
            'governorate_id' => $governorate->id,
            'name' => 'القاهرة الجديدة',
        ]);

        // Find or create geographic hierarchy
        $district = $this->findOrCreateDistrict($districtName, $city);
        $zone = $this->findOrCreateZone($zoneName, $district);
        $area = $this->findOrCreateArea($areaName, $zone);

        // Find or create storage hierarchy from Excel data
        $room = $this->findOrCreateRoom($roomName);
        $lane = $this->findOrCreateLane($laneName, $room);
        $stand = $this->findOrCreateStand($standName, $lane);
        $rack = $this->findOrCreateRack($rackName, $stand);

        // Parse land numbers - can be multiple in one cell
        $landNumbers = $this->parseLandNumbers($landNo);

        // If no land numbers found, create a default one
        if (empty($landNumbers)) {
            $landNumbers = ['قطعة-' . $client->id . '-' . time()];
        }

        // Create lands for all parsed land numbers
        $lands = [];
        foreach ($landNumbers as $landNoValue) {
            $landData = [
                'client_id' => $client->id,
                'governorate_id' => $governorate->id,
                'city_id' => $city->id,
                'district_id' => $district?->id,
                'zone_id' => $zone?->id,
                'area_id' => $area?->id,
                'room_id' => $room?->id,
                'lane_id' => $lane?->id,
                'stand_id' => $stand?->id,
                'rack_id' => $rack?->id,
                'land_no' => $landNoValue,
            ];

            $land = null;
            if ($this->updateExisting) {
                $land = Land::updateOrCreate(
                    [
                        'client_id' => $client->id,
                        'land_no' => $landNoValue,
                    ],
                    $landData
                );
            } else {
                // Check if land exists for this client with this land_no
                $land = Land::where('client_id', $client->id)
                    ->where('land_no', $landNoValue)
                    ->first();

                if (!$land) {
                    $land = Land::create($landData);
                }
            }

            $lands[] = $land;
        }

        // Normalize file name - use 'لا يوجد' if empty
        $fileName = 'لا يوجد';
        if (!empty($fileNameCol)) {
            $trimmedFileName = trim($fileNameCol);
            if ($trimmedFileName !== '' && $trimmedFileName !== 'لا يوجد' && strtolower($trimmedFileName) !== 'null') {
                $fileName = $trimmedFileName;
            }
        }

        // Create file for each land created from this row
        if (!empty($lands)) {
            foreach ($lands as $land) {
                // Check if file already exists for this client/land combination
                $existingFile = File::where('client_id', $client->id)
                    ->where('land_id', $land->id)
                    ->where('file_name', $fileName)
                    ->first();

                if (!$existingFile) {
                    // Generate unique barcode using File model method
                    $barcode = File::generateBarcode();

                    File::create([
                        'client_id' => $client->id,
                        'land_id' => $land->id,
                        'room_id' => $room?->id,
                        'lane_id' => $lane?->id,
                        'stand_id' => $stand?->id,
                        'rack_id' => $rack?->id,
                        'file_name' => $fileName, // null if empty or "لا يوجد"
                        'barcode' => $barcode,
                        'status' => 'pending',
                        'uploaded_by' => $this->import->user_id,
                    ]);
                }
            }
        }

        Log::debug("Archive import row processed", [
            'client' => $ownerName,
            'lands_created' => count($lands),
            'land_numbers' => array_map(fn($l) => $l->land_no, $lands),
            'file_name' => $fileName,
            'file_created' => !empty($lands),
            'room' => $roomName,
            'lane' => $laneName,
            'stand' => $standName,
            'rack' => $rackName,
        ]);
    }

    /**
     * Parse land numbers from a cell that may contain multiple entries
     * Handles formats like:
     * - "نموذج 6"
     * - "(225) 12 نموذج"
     * - "(236+368+388+325+28) 34 نموذج"
     * - Multiple lines: "نموذج 54\nنموذج 44\nنموذج 8"
     * - Ranges: "نموذج 1-5" or "1-5 نموذج"
     */
    private function parseLandNumbers(?string $landNoCell): array
    {
        if (empty($landNoCell)) {
            return [];
        }

        $landNumbers = [];

        // Split by newlines first to handle multiple entries
        $lines = preg_split('/[\r\n]+/', trim($landNoCell));

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Pattern 1: "(numbers) X نموذج" - e.g., "(225) 12 نموذج" or "(236+368+388) 34 نموذج"
            if (preg_match('/\(([^)]+)\)\s*(\d+)\s*نموذج/u', $line, $matches)) {
                $numbers = $matches[1];
                $modelNumber = $matches[2];

                // If numbers contain + or other separators, split them
                $subNumbers = preg_split('/[+,،\s]+/', $numbers);
                foreach ($subNumbers as $subNum) {
                    $subNum = trim($subNum);
                    if (is_numeric($subNum)) {
                        $landNumbers[] = "نموذج {$modelNumber} ({$subNum})";
                    }
                }
                continue;
            }

            // Pattern 2: "نموذج X" - e.g., "نموذج 54"
            if (preg_match('/نموذج\s+(\d+)/u', $line, $matches)) {
                $landNumbers[] = "نموذج {$matches[1]}";
                continue;
            }

            // Pattern 3: "X نموذج" - e.g., "54 نموذج"
            if (preg_match('/(\d+)\s+نموذج/u', $line, $matches)) {
                $landNumbers[] = "نموذج {$matches[1]}";
                continue;
            }

            // Pattern 4: Range format "نموذج 1-5" or "1-5 نموذج"
            if (preg_match('/نموذج\s+(\d+)\s*-\s*(\d+)/u', $line, $matches)) {
                $start = (int)$matches[1];
                $end = (int)$matches[2];
                for ($i = $start; $i <= $end; $i++) {
                    $landNumbers[] = "نموذج {$i}";
                }
                continue;
            }

            if (preg_match('/(\d+)\s*-\s*(\d+)\s+نموذج/u', $line, $matches)) {
                $start = (int)$matches[1];
                $end = (int)$matches[2];
                for ($i = $start; $i <= $end; $i++) {
                    $landNumbers[] = "نموذج {$i}";
                }
                continue;
            }

            // If no pattern matched but line has content, use it as-is
            if (!empty($line)) {
                $landNumbers[] = $line;
            }
        }

        // Remove duplicates and return
        return array_unique($landNumbers);
    }

    private function findOrCreateRoom(?string $name): ?Room
    {
        if (empty($name)) return null;
        return Room::firstOrCreate(
            ['name' => trim($name)],
            ['name' => trim($name), 'building_name' => 'المبنى الرئيسي']
        );
    }

    private function findOrCreateLane(?string $name, ?Room $room): ?Lane
    {
        if (empty($name) || !$room) return null;
        return Lane::firstOrCreate([
            'room_id' => $room->id,
            'name' => trim($name),
        ]);
    }

    private function findOrCreateStand(?string $name, ?Lane $lane): ?Stand
    {
        if (empty($name) || !$lane) return null;
        return Stand::firstOrCreate([
            'lane_id' => $lane->id,
            'name' => trim($name),
        ]);
    }

    private function findOrCreateRack(?string $name, ?Stand $stand): ?Rack
    {
        if (empty($name) || !$stand) return null;
        return Rack::firstOrCreate([
            'stand_id' => $stand->id,
            'name' => trim($name),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessImportJob permanently failed for import ID: {$this->import->id}: " . $exception->getMessage());

        $this->import->update([
            'status' => 'failed',
            'completed_at' => now(),
            'errors' => ['general' => 'فشل الاستيراد: ' . $exception->getMessage()],
        ]);
    }

    /**
     * Map Arabic headers to English keys for archive imports
     * Handles variations in spelling and spacing of Arabic column names
     */
    private function mapArabicHeaders(array $headers): array
    {
        $mapping = [
            // Actual file headers (with variations)
            'رقم الملف' => 'file_name_col',
            'رقم' => 'file_number',
            'الملف' => 'file_name_col',
            'المالك' => 'owner_name',
            'الاسم' => 'owner_name',
            // Land number variations
            'رقم القطعه' => 'land_no',
            'القطعه' => 'land_no',
            'القطعة' => 'land_no',
            'قطعه فرعية' => 'land_no',
            'فرعية' => 'land_no',
            // Geographic
            'الحي' => 'district',
            'الحى' => 'district',
            'المنطقة' => 'zone',
            'المجاورة' => 'area',
            'المحافظة' => 'governorate',
            // Physical location
            'الاوضة' => 'room',
            'الاوضه' => 'room',
            'الممر' => 'lane',
            'الاستند' => 'stand',
            'الرف' => 'rack',
            // Other fields
            'كود' => 'client_code',
            'الوظيفة' => 'job',
            'العمر' => 'age',
            'الأسرة' => 'family',
            'الدور' => 'floor',
            // Common client/owner names that might appear as headers
            'الجميع الخامس' => 'owner_name',
            'اللوتس' => 'owner_name',
        ];

        $mappedHeaders = [];
        foreach ($headers as $header) {
            $trimmed = trim($header);
            $mappedHeaders[] = $mapping[$trimmed] ?? strtolower($trimmed);
        }

        return $mappedHeaders;
    }
}
