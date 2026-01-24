<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Land;
use App\Models\Client;
use App\Models\Governorate;
use App\Models\City;
use App\Models\District;
use App\Models\Zone;
use App\Models\Area;
use App\Models\ActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LandController extends Controller
{
    /**
     * Display a listing of lands with hierarchy view
     */
    public function index(Request $request)
    {
        $query = Land::with(['client', 'governorate', 'city', 'district', 'zone', 'area', 'room', 'lane', 'stand', 'rack']);

        // Handle trashed filter
        if ($request->input('trashed') === 'only') {
            $query->onlyTrashed();
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('land_no', 'like', "%{$search}%")
                  ->orWhere('unit_no', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Client filter
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Governorate filter
        if ($request->filled('governorate_id')) {
            $query->where('governorate_id', $request->governorate_id);
        }

        // City filter
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        $lands = $query->latest()->paginate(15)->withQueryString();

        // Get hierarchy data for nested view
        $governorates = Governorate::withCount('lands')->get();
        $clients = Client::orderBy('name')->get();

        // Statistics
        $totalLands = Land::count();
        $trashedLands = Land::onlyTrashed()->count();
        $landsWithFiles = Land::has('files')->count();

        return view('admin.lands.index', compact(
            'lands',
            'governorates',
            'clients',
            'totalLands',
            'trashedLands',
            'landsWithFiles'
        ));
    }

    /**
     * Store a newly created land
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'governorate_id' => 'nullable|exists:governorates,id',
            'city_id' => 'nullable|exists:cities,id',
            'district_id' => 'nullable|exists:districts,id',
            'zone_id' => 'nullable|exists:zones,id',
            'area_id' => 'nullable|exists:areas,id',
            'room_id' => 'nullable|exists:rooms,id',
            'lane_id' => 'nullable|exists:lanes,id',
            'stand_id' => 'nullable|exists:stands,id',
            'rack_id' => 'nullable|exists:racks,id',
            'land_no' => 'required|string|max:255',
            'unit_no' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $land = Land::create($request->all());

            // Log create activity
            ActivityLogger::created($land, ActivityLog::GROUP_GEOGRAPHIC, ['land_no' => $land->land_no]);

            DB::commit();
            return redirect()->route('admin.lands.index')->with('success', 'تم إضافة الأرض بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Land store error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إضافة الأرض')->withInput();
        }
    }

    /**
     * Display the specified land
     */
    public function show(Land $land)
    {
        $land->load(['client', 'governorate', 'city', 'district', 'zone', 'area', 'room', 'lane', 'stand', 'rack', 'files']);
        return view('admin.lands.show', compact('land'));
    }

    /**
     * Update the specified land
     */
    public function update(Request $request, Land $land)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'governorate_id' => 'nullable|exists:governorates,id',
            'city_id' => 'nullable|exists:cities,id',
            'district_id' => 'nullable|exists:districts,id',
            'zone_id' => 'nullable|exists:zones,id',
            'area_id' => 'nullable|exists:areas,id',
            'room_id' => 'nullable|exists:rooms,id',
            'lane_id' => 'nullable|exists:lanes,id',
            'stand_id' => 'nullable|exists:stands,id',
            'rack_id' => 'nullable|exists:racks,id',
            'land_no' => 'required|string|max:255',
            'unit_no' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $oldValues = $land->only(['land_no', 'unit_no', 'address']);
            $land->update($request->all());
            $newValues = $land->only(['land_no', 'unit_no', 'address']);

            // Log update activity
            ActivityLogger::updated($land, ActivityLog::GROUP_GEOGRAPHIC, $oldValues, $newValues);

            DB::commit();
            return redirect()->route('admin.lands.index')->with('success', 'تم تحديث بيانات الأرض بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Land update error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحديث بيانات الأرض')->withInput();
        }
    }

    /**
     * Soft delete the specified land
     */
    public function destroy(Land $land)
    {
        try {
            // Log delete activity
            ActivityLogger::deleted($land, ActivityLog::GROUP_GEOGRAPHIC);

            $land->delete();
            return redirect()->route('admin.lands.index')->with('success', 'تم حذف الأرض بنجاح');
        } catch (\Exception $e) {
            Log::error('Land delete error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف الأرض');
        }
    }

    /**
     * Restore the specified soft-deleted land
     */
    public function restore($id)
    {
        try {
            $land = Land::onlyTrashed()->findOrFail($id);
            $land->restore();

            // Log restore activity
            ActivityLogger::make()
                ->action('restore', ActivityLog::GROUP_GEOGRAPHIC)
                ->on($land)
                ->description("استعادة الأرض: {$land->land_no}")
                ->log();

            return redirect()->route('admin.lands.index')->with('success', 'تم استعادة الأرض بنجاح');
        } catch (\Exception $e) {
            Log::error('Land restore error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء استعادة الأرض');
        }
    }

    /**
     * Permanently delete the specified land
     */
    public function forceDelete($id)
    {
        try {
            $land = Land::onlyTrashed()->findOrFail($id);
            $landNo = $land->land_no;

            // Log force delete activity
            ActivityLogger::make()
                ->action('force_delete', ActivityLog::GROUP_GEOGRAPHIC)
                ->description("حذف نهائي للأرض: {$landNo}")
                ->withProperties(['land_id' => $id, 'land_no' => $landNo])
                ->log();

            $land->forceDelete();
            return redirect()->route('admin.lands.index')->with('success', 'تم حذف الأرض نهائياً');
        } catch (\Exception $e) {
            Log::error('Land force delete error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء الحذف النهائي للأرض');
        }
    }

    /**
     * Bulk delete lands
     */
    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:lands,id']);

        try {
            $count = count($request->ids);
            Land::whereIn('id', $request->ids)->delete();

            // Log bulk delete activity
            ActivityLogger::bulkDeleted(
                "حذف {$count} أرض",
                $request->ids,
                ActivityLog::GROUP_GEOGRAPHIC
            );

            return response()->json(['success' => true, 'message' => 'تم حذف الأراضي المحددة بنجاح']);
        } catch (\Exception $e) {
            Log::error('Bulk delete lands error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء الحذف'], 500);
        }
    }

    /**
     * Bulk restore lands
     */
    public function bulkRestore(Request $request)
    {
        $request->validate(['ids' => 'required|array']);

        try {
            $count = Land::onlyTrashed()->whereIn('id', $request->ids)->restore();

            // Log bulk restore activity
            ActivityLogger::make()
                ->action('bulk_restore', ActivityLog::GROUP_GEOGRAPHIC)
                ->description("استعادة {$count} أرض")
                ->withAffectedIds($request->ids)
                ->batch(null, $count)
                ->log();

            return response()->json(['success' => true, 'message' => 'تم استعادة الأراضي المحددة بنجاح']);
        } catch (\Exception $e) {
            Log::error('Bulk restore lands error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء الاستعادة'], 500);
        }
    }

    /**
     * Bulk force delete lands
     */
    public function bulkForceDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array']);

        try {
            $count = count($request->ids);

            // Log bulk force delete activity
            ActivityLogger::make()
                ->action('bulk_force_delete', ActivityLog::GROUP_GEOGRAPHIC)
                ->description("حذف نهائي لـ {$count} أرض")
                ->withAffectedIds($request->ids)
                ->batch(null, $count)
                ->log();

            Land::onlyTrashed()->whereIn('id', $request->ids)->forceDelete();
            return response()->json(['success' => true, 'message' => 'تم حذف الأراضي نهائياً']);
        } catch (\Exception $e) {
            Log::error('Bulk force delete lands error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء الحذف النهائي'], 500);
        }
    }

    /**
     * Get cities by governorate (API)
     */
    public function getCities(Governorate $governorate)
    {
        return response()->json($governorate->cities()->orderBy('name')->get(['id', 'name']));
    }

    /**
     * Get districts by city (API)
     */
    public function getDistricts(City $city)
    {
        return response()->json($city->districts()->orderBy('name')->get(['id', 'name']));
    }

    /**
     * Get zones by district (API)
     */
    public function getZones(District $district)
    {
        return response()->json($district->zones()->orderBy('name')->get(['id', 'name']));
    }

    /**
     * Get areas by zone (API)
     */
    public function getAreas(Zone $zone)
    {
        return response()->json($zone->areas()->orderBy('name')->get(['id', 'name']));
    }
}
