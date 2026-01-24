<?php

namespace App\Services\Import;

use App\Models\Area;
use App\Models\City;
use App\Models\Client;
use App\Models\District;
use App\Models\File;
use App\Models\Governorate;
use App\Models\Import;
use App\Models\Land;
use App\Models\Lane;
use App\Models\Rack;
use App\Models\Room;
use App\Models\Stand;
use App\Models\Zone;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Core import service with optimized data processing.
 *
 * Architecture decisions:
 * 1. In-memory caching: All lookup entities are preloaded once to avoid N+1 queries
 * 2. Batch inserts: Uses DB::table()->insert() for bulk operations
 * 3. Idempotency: Uses unique keys to prevent duplicates on retry
 * 4. Transaction batching: Commits every N rows to balance safety and performance
 */
class ImportService
{
    // Header mappings (Arabic to English keys)
    private const HEADER_MAP = [
        'رقم' => 'row_number',
        'الملف' => 'file_name',
        'المالك' => 'client_name',
        'القطعه' => 'land_no',
        'القطعة' => 'land_no',
        'الحى' => 'district',
        'الحي' => 'district',
        'المنطقة' => 'zone',
        'المجاورة' => 'area',
        'الاوضة' => 'room',
        'الوحدة' => 'room',
        'الممر' => 'lane',
        'الاستند' => 'stand',
        'الرف' => 'rack',
    ];

    // In-memory caches for lookup entities
    private Collection $governoratesCache;
    private Collection $citiesCache;
    private Collection $districtsCache;
    private Collection $zonesCache;
    private Collection $areasCache;
    private Collection $roomsCache;
    private Collection $lanesCache;
    private Collection $standsCache;
    private Collection $racksCache;
    private Collection $clientsCache;
    private Collection $landsCache;

    // Batch buffers for bulk inserts
    private array $clientsToInsert = [];
    private array $landsToInsert = [];
    private array $filesToInsert = [];

    // Tracking
    private int $successCount = 0;
    private int $failedCount = 0;
    private array $errors = [];
    private Import $import;

    // Configuration
    private int $batchInsertSize = 100;
    private int $commitInterval = 50;
    private bool $skipErrors = true;

    public function __construct()
    {
        $this->initializeCaches();
    }

    /**
     * Initialize empty cache collections.
     */
    private function initializeCaches(): void
    {
        $this->governoratesCache = collect();
        $this->citiesCache = collect();
        $this->districtsCache = collect();
        $this->zonesCache = collect();
        $this->areasCache = collect();
        $this->roomsCache = collect();
        $this->lanesCache = collect();
        $this->standsCache = collect();
        $this->racksCache = collect();
        $this->clientsCache = collect();
        $this->landsCache = collect();
    }

    /**
     * Preload all lookup data into memory.
     *
     * Why: Instead of querying the database for each row (N+1 problem),
     * we load everything once at the start. For 12,000 rows with 10
     * lookups each, this reduces queries from 120,000+ to ~11.
     */
    public function preloadCaches(): void
    {
        Log::info('[Import] Preloading caches...');

        // Disable query logging to save memory
        DB::disableQueryLog();

        $this->governoratesCache = Governorate::all()->keyBy('name');
        $this->citiesCache = City::all()->keyBy(fn($c) => $c->governorate_id . ':' . $c->name);
        $this->districtsCache = District::all()->keyBy(fn($d) => $d->city_id . ':' . $d->name);
        $this->zonesCache = Zone::all()->keyBy(fn($z) => $z->district_id . ':' . $z->name);
        $this->areasCache = Area::all()->keyBy(fn($a) => $a->zone_id . ':' . $a->name);
        $this->roomsCache = Room::all()->keyBy('name');
        $this->lanesCache = Lane::all()->keyBy(fn($l) => $l->room_id . ':' . $l->name);
        $this->standsCache = Stand::all()->keyBy(fn($s) => $s->lane_id . ':' . $s->name);
        $this->racksCache = Rack::all()->keyBy(fn($r) => $r->stand_id . ':' . $r->name);
        $this->clientsCache = Client::all()->keyBy('name');
        $this->landsCache = Land::all()->keyBy(fn($l) => $l->client_id . ':' . $l->land_no . ':' . ($l->governorate_id ?? 0));

        Log::info('[Import] Caches preloaded', [
            'governorates' => $this->governoratesCache->count(),
            'cities' => $this->citiesCache->count(),
            'districts' => $this->districtsCache->count(),
            'zones' => $this->zonesCache->count(),
            'areas' => $this->areasCache->count(),
            'rooms' => $this->roomsCache->count(),
            'lanes' => $this->lanesCache->count(),
            'stands' => $this->standsCache->count(),
            'racks' => $this->racksCache->count(),
            'clients' => $this->clientsCache->count(),
            'lands' => $this->landsCache->count(),
        ]);
    }

    /**
     * Set the import model for tracking progress.
     */
    public function setImport(Import $import): self
    {
        $this->import = $import;
        $this->skipErrors = true; // Can be made configurable
        return $this;
    }

    /**
     * Process a chunk of rows.
     *
     * Why chunking: Processing in batches allows us to:
     * 1. Commit transactions periodically (prevents long locks)
     * 2. Update progress incrementally
     * 3. Handle partial failures without losing all work
     */
    public function processChunk(array $rows, string $importType): array
    {
        $chunkSuccess = 0;
        $chunkFailed = 0;
        $chunkErrors = [];

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                try {
                    $normalizedRow = $this->normalizeRow($row);

                    switch ($importType) {
                        case 'archive':
                            $this->processArchiveRow($normalizedRow);
                            break;
                        case 'full':
                            $this->processFullRow($normalizedRow);
                            break;
                        case 'clients':
                            $this->processClientRow($normalizedRow);
                            break;
                        case 'lands':
                            $this->processLandRow($normalizedRow);
                            break;
                        case 'geographic':
                            $this->processGeographicRow($normalizedRow);
                            break;
                        default:
                            $this->processArchiveRow($normalizedRow);
                    }

                    $chunkSuccess++;
                    $this->successCount++;

                } catch (\Throwable $e) {
                    $chunkFailed++;
                    $this->failedCount++;

                    $rowNumber = $row['_excel_row'] ?? ($index + 1);
                    // Sanitize error message to valid UTF-8
                    $errorMsg = $this->sanitizeUtf8($e->getMessage());
                    $chunkErrors[$rowNumber] = $errorMsg;
                    $this->errors[$rowNumber] = $errorMsg;

                    Log::warning("[Import] Row {$rowNumber} failed: " . $errorMsg);

                    if (!$this->skipErrors) {
                        throw $e;
                    }
                }
            }

            // Flush any remaining batch inserts
            $this->flushBatchInserts();

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[Import] Chunk failed, rolling back: ' . $e->getMessage());
            throw $e;
        }

        return [
            'success' => $chunkSuccess,
            'failed' => $chunkFailed,
            'errors' => $chunkErrors,
        ];
    }

    /**
     * Normalize row keys from Arabic to English.
     */
    private function normalizeRow(array $row): array
    {
        $normalized = [];

        foreach ($row as $key => $value) {
            // Trim the key to remove any trailing/leading spaces
            $trimmedKey = trim($key);
            $normalizedKey = self::HEADER_MAP[$trimmedKey] ?? $trimmedKey;
            $normalized[$normalizedKey] = $this->cleanValue($value);
        }

        // Keep original excel row for error reporting
        if (isset($row['_excel_row'])) {
            $normalized['_excel_row'] = $row['_excel_row'];
        }

        return $normalized;
    }

    /**
     * Clean and normalize a value.
     */
    private function cleanValue($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '' || strtolower($value) === 'null') {
            return null;
        }

        // Remove invalid UTF-8 bytes
        $value = @iconv('UTF-8', 'UTF-8//IGNORE', $value);

        return $value;
    }

    /**
     * Sanitize string to valid UTF-8.
     */
    private function sanitizeUtf8(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        // If already valid UTF-8, return as-is
        if (mb_check_encoding($value, 'UTF-8')) {
            // Just remove control characters
            $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);
            return $value ?: null;
        }

        // Try to convert from common encodings
        $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');

        // If still invalid, use iconv to strip bad bytes
        if (!mb_check_encoding($value, 'UTF-8')) {
            $value = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
        }

        // Remove control characters
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);

        return $value ?: null;
    }

    /**
     * Process an archive row (main import type).
     * Creates: Client -> Land -> File with geographic and physical location.
     */
    private function processArchiveRow(array $row): void
    {
        // Handle multiple possible field names (Arabic headers or pre-mapped English)
        $fileName = $row['file_name'] ?? $row['file_name_col'] ?? $row['الملف'] ?? null;
        $clientName = $row['client_name'] ?? $row['owner_name'] ?? $row['المالك'] ?? 'غير معروف';
        $landNo = $row['land_no'] ?? $row['القطعه'] ?? $row['القطعة'] ?? null;
        $districtName = $row['district'] ?? $row['الحي'] ?? $row['الحى'] ?? 'غير محدد';
        $zoneName = $row['zone'] ?? $row['المنطقة'] ?? 'غير محدد';
        $areaName = $row['area'] ?? $row['المجاورة'] ?? 'غير محدد';
        $roomName = $row['room'] ?? $row['الاوضة'] ?? $row['الوحدة'] ?? null;
        $laneName = $row['lane'] ?? $row['الممر'] ?? null;
        $standName = $row['stand'] ?? $row['الاستند'] ?? null;
        $rackName = $row['rack'] ?? $row['الرف'] ?? null;

        // Set defaults for empty values
        if (empty($fileName)) {
            $fileName = 'لا يوجد';
        }
        if (empty($landNo)) {
            $landNo = 'غير محدد';
        }

        // Get or create geographic hierarchy
        $governorate = $this->getOrCreateGovernorate('القاهرة الجديدة');
        $city = $this->getOrCreateCity($governorate->id, 'القاهرة الجديدة');
        $district = $this->getOrCreateDistrict($city->id, $districtName);
        $zone = $this->getOrCreateZone($district->id, $zoneName);
        $area = $this->getOrCreateArea($zone->id, $areaName);

        // Get or create physical location hierarchy
        $room = $this->getOrCreateRoom($roomName);
        $lane = $room ? $this->getOrCreateLane($room->id, $laneName) : null;
        $stand = $lane ? $this->getOrCreateStand($lane->id, $standName) : null;
        $rack = $stand ? $this->getOrCreateRack($stand->id, $rackName) : null;

        // Get or create client
        $client = $this->getOrCreateClient($clientName, $row['_excel_row'] ?? null);

        // Parse land numbers (can be comma-separated or range like "1-5")
        $landNumbers = $this->parseLandNumbers($landNo);

        foreach ($landNumbers as $parsedLandNo) {
            // Get or create land
            $land = $this->getOrCreateLand([
                'client_id' => $client->id,
                'governorate_id' => $governorate->id,
                'city_id' => $city->id,
                'district_id' => $district->id,
                'zone_id' => $zone->id,
                'area_id' => $area->id,
                'room_id' => $room?->id,
                'lane_id' => $lane?->id,
                'stand_id' => $stand?->id,
                'rack_id' => $rack?->id,
                'land_no' => $parsedLandNo,
            ]);

            // Create file record
            $this->createFile([
                'client_id' => $client->id,
                'land_id' => $land->id,
                'room_id' => $room?->id,
                'lane_id' => $lane?->id,
                'stand_id' => $stand?->id,
                'rack_id' => $rack?->id,
                'file_name' => $fileName,
                'original_name' => $fileName,
                'status' => 'completed',
            ]);
        }
    }

    /**
     * Process full import row.
     */
    private function processFullRow(array $row): void
    {
        $this->processArchiveRow($row);
    }

    /**
     * Process client-only row.
     */
    private function processClientRow(array $row): void
    {
        $clientName = $row['client_name'] ?? $row['name'] ?? null;
        if (empty($clientName)) {
            return;
        }

        $this->getOrCreateClient($clientName, $row['_excel_row'] ?? null);
    }

    /**
     * Process land-only row.
     */
    private function processLandRow(array $row): void
    {
        // Requires client reference
        $clientName = $row['client_name'] ?? null;
        if (empty($clientName)) {
            return;
        }

        $client = $this->getOrCreateClient($clientName);
        $governorate = $this->getOrCreateGovernorate($row['governorate'] ?? 'القاهرة الجديدة');

        $this->getOrCreateLand([
            'client_id' => $client->id,
            'governorate_id' => $governorate->id,
            'land_no' => $row['land_no'] ?? 'غير محدد',
        ]);
    }

    /**
     * Process geographic-only row.
     */
    private function processGeographicRow(array $row): void
    {
        $governorate = $this->getOrCreateGovernorate($row['governorate'] ?? 'القاهرة الجديدة');
        $city = $this->getOrCreateCity($governorate->id, $row['city'] ?? $row['governorate'] ?? 'غير محدد');
        $district = $this->getOrCreateDistrict($city->id, $row['district'] ?? 'غير محدد');
        $zone = $this->getOrCreateZone($district->id, $row['zone'] ?? 'غير محدد');
        $this->getOrCreateArea($zone->id, $row['area'] ?? 'غير محدد');
    }

    // =====================================================
    // CACHED GETTERS WITH AUTO-CREATE (Idempotent)
    // =====================================================

    private function getOrCreateGovernorate(string $name): Governorate
    {
        if ($this->governoratesCache->has($name)) {
            return $this->governoratesCache->get($name);
        }

        $governorate = Governorate::firstOrCreate(['name' => $name]);
        $this->governoratesCache->put($name, $governorate);

        return $governorate;
    }

    private function getOrCreateCity(int $governorateId, string $name): City
    {
        $key = "{$governorateId}:{$name}";

        if ($this->citiesCache->has($key)) {
            return $this->citiesCache->get($key);
        }

        $city = City::firstOrCreate([
            'governorate_id' => $governorateId,
            'name' => $name,
        ]);
        $this->citiesCache->put($key, $city);

        return $city;
    }

    private function getOrCreateDistrict(int $cityId, string $name): District
    {
        $key = "{$cityId}:{$name}";

        if ($this->districtsCache->has($key)) {
            return $this->districtsCache->get($key);
        }

        $district = District::firstOrCreate([
            'city_id' => $cityId,
            'name' => $name,
        ]);
        $this->districtsCache->put($key, $district);

        return $district;
    }

    private function getOrCreateZone(int $districtId, string $name): Zone
    {
        $key = "{$districtId}:{$name}";

        if ($this->zonesCache->has($key)) {
            return $this->zonesCache->get($key);
        }

        $zone = Zone::firstOrCreate([
            'district_id' => $districtId,
            'name' => $name,
        ]);
        $this->zonesCache->put($key, $zone);

        return $zone;
    }

    private function getOrCreateArea(int $zoneId, string $name): Area
    {
        $key = "{$zoneId}:{$name}";

        if ($this->areasCache->has($key)) {
            return $this->areasCache->get($key);
        }

        $area = Area::firstOrCreate([
            'zone_id' => $zoneId,
            'name' => $name,
        ]);
        $this->areasCache->put($key, $area);

        return $area;
    }

    private function getOrCreateRoom(?string $name): ?Room
    {
        if (empty($name)) {
            return null;
        }

        if ($this->roomsCache->has($name)) {
            return $this->roomsCache->get($name);
        }

        $room = Room::firstOrCreate(
            ['name' => $name],
            ['building_name' => 'المبنى الرئيسي']
        );
        $this->roomsCache->put($name, $room);

        return $room;
    }

    private function getOrCreateLane(int $roomId, ?string $name): ?Lane
    {
        if (empty($name)) {
            return null;
        }

        $key = "{$roomId}:{$name}";

        if ($this->lanesCache->has($key)) {
            return $this->lanesCache->get($key);
        }

        $lane = Lane::firstOrCreate([
            'room_id' => $roomId,
            'name' => $name,
        ]);
        $this->lanesCache->put($key, $lane);

        return $lane;
    }

    private function getOrCreateStand(int $laneId, ?string $name): ?Stand
    {
        if (empty($name)) {
            return null;
        }

        $key = "{$laneId}:{$name}";

        if ($this->standsCache->has($key)) {
            return $this->standsCache->get($key);
        }

        $stand = Stand::firstOrCreate([
            'lane_id' => $laneId,
            'name' => $name,
        ]);
        $this->standsCache->put($key, $stand);

        return $stand;
    }

    private function getOrCreateRack(int $standId, ?string $name): ?Rack
    {
        if (empty($name)) {
            return null;
        }

        $key = "{$standId}:{$name}";

        if ($this->racksCache->has($key)) {
            return $this->racksCache->get($key);
        }

        $rack = Rack::firstOrCreate([
            'stand_id' => $standId,
            'name' => $name,
        ]);
        $this->racksCache->put($key, $rack);

        return $rack;
    }

    private function getOrCreateClient(string $name, ?int $excelRow = null): Client
    {
        if ($this->clientsCache->has($name)) {
            return $this->clientsCache->get($name);
        }

        $client = Client::firstOrCreate(
            ['name' => $name],
            [
                'client_code' => Client::generateClientCode(),
                'excel_row_number' => $excelRow,
            ]
        );
        $this->clientsCache->put($name, $client);

        return $client;
    }

    private function getOrCreateLand(array $data): Land
    {
        $landNo = $data['land_no'] ?? 'غير محدد';
        $key = $data['client_id'] . ':' . $landNo . ':' . ($data['governorate_id'] ?? 0);

        if ($this->landsCache->has($key)) {
            return $this->landsCache->get($key);
        }

        $land = Land::firstOrCreate(
            [
                'client_id' => $data['client_id'],
                'land_no' => $landNo,
                'governorate_id' => $data['governorate_id'] ?? null,
            ],
            $data
        );
        $this->landsCache->put($key, $land);

        return $land;
    }

    /**
     * Create a file record.
     * Uses batch insert buffer for efficiency.
     */
    private function createFile(array $data): void
    {
        // Generate unique barcode
        $data['barcode'] = File::generateBarcode();
        $data['created_at'] = now();
        $data['updated_at'] = now();

        $this->filesToInsert[] = $data;

        // Flush when batch size reached
        if (count($this->filesToInsert) >= $this->batchInsertSize) {
            $this->flushFileInserts();
        }
    }

    /**
     * Flush all pending batch inserts.
     */
    private function flushBatchInserts(): void
    {
        $this->flushFileInserts();
    }

    /**
     * Flush file batch inserts.
     */
    private function flushFileInserts(): void
    {
        if (empty($this->filesToInsert)) {
            return;
        }

        // Use chunked insert for very large batches
        $chunks = array_chunk($this->filesToInsert, 500);
        foreach ($chunks as $chunk) {
            DB::table('files')->insert($chunk);
        }

        $this->filesToInsert = [];
    }

    /**
     * Parse land numbers - just return as-is to preserve Arabic text.
     */
    private function parseLandNumbers(?string $landNo): array
    {
        if (empty($landNo)) {
            return ['غير محدد'];
        }

        // Return as-is to preserve Arabic text exactly
        return [trim($landNo)];
    }

    /**
     * Get import statistics.
     */
    public function getStats(): array
    {
        return [
            'success' => $this->successCount,
            'failed' => $this->failedCount,
            'errors' => $this->errors,
        ];
    }

    /**
     * Update import progress in database.
     */
    public function updateProgress(int $processedRows): void
    {
        if (!isset($this->import)) {
            return;
        }

        $this->import->update([
            'processed_rows' => $processedRows,
            'success_rows' => $this->successCount,
            'failed_rows' => $this->failedCount,
            'errors' => $this->errors,
        ]);
    }

    /**
     * Clear caches to free memory.
     */
    public function clearCaches(): void
    {
        $this->governoratesCache = collect();
        $this->citiesCache = collect();
        $this->districtsCache = collect();
        $this->zonesCache = collect();
        $this->areasCache = collect();
        $this->roomsCache = collect();
        $this->lanesCache = collect();
        $this->standsCache = collect();
        $this->racksCache = collect();
        $this->clientsCache = collect();
        $this->landsCache = collect();

        gc_collect_cycles();
    }
}
