<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use App\Models\City;
use App\Models\District;
use App\Models\Zone;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GeographicAreaController extends Controller
{
    /**
     * Display the geographic areas hierarchy
     */
    public function index()
    {
        $governorates = Governorate::withCount(['cities', 'lands'])
            ->with(['cities' => function ($q) {
                $q->withCount(['districts', 'lands'])
                  ->with(['districts' => function ($q2) {
                      $q2->withCount(['zones', 'lands'])
                        ->with(['zones' => function ($q3) {
                            $q3->withCount(['areas', 'lands'])
                              ->with(['areas' => function ($q4) {
                                  $q4->withCount('lands');
                              }]);
                        }]);
                  }]);
            }])
            ->orderBy('name')
            ->get();

        // Statistics
        $stats = [
            'governorates' => Governorate::count(),
            'cities' => City::count(),
            'districts' => District::count(),
            'zones' => Zone::count(),
            'areas' => Area::count(),
        ];

        return view('admin.geographic-areas.index', compact('governorates', 'stats'));
    }

    // ==================== GOVERNORATES ====================

    /**
     * Show governorate with full hierarchy (JSON)
     */
    public function showGovernorate(Governorate $governorate)
    {
        $governorate->load(['cities' => function ($q) {
            $q->with(['districts' => function ($q2) {
                $q2->with(['zones' => function ($q3) {
                    $q3->with('areas');
                }]);
            }]);
        }]);

        return response()->json([
            'success' => true,
            'governorate' => $governorate
        ]);
    }

    /**
     * Store a new governorate
     */
    public function storeGovernorate(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:governorates,name']);

        try {
            Governorate::create(['name' => $request->name]);
            return redirect()->route('admin.geographic-areas.index')->with('success', 'تم إضافة المحافظة بنجاح');
        } catch (\Exception $e) {
            Log::error('Store governorate error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إضافة المحافظة')->withInput();
        }
    }

    /**
     * Update a governorate
     */
    public function updateGovernorate(Request $request, Governorate $governorate)
    {
        $request->validate(['name' => 'required|string|max:255|unique:governorates,name,' . $governorate->id]);

        try {
            $governorate->update(['name' => $request->name]);
            return redirect()->route('admin.geographic-areas.index')->with('success', 'تم تحديث المحافظة بنجاح');
        } catch (\Exception $e) {
            Log::error('Update governorate error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحديث المحافظة');
        }
    }

    /**
     * Delete a governorate
     */
    public function destroyGovernorate(Governorate $governorate)
    {
        if ($governorate->cities()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف المحافظة لأنها تحتوي على مدن');
        }

        try {
            $governorate->delete();
            return redirect()->route('admin.geographic-areas.index')->with('success', 'تم حذف المحافظة بنجاح');
        } catch (\Exception $e) {
            Log::error('Delete governorate error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف المحافظة');
        }
    }

    // ==================== CITIES ====================

    /**
     * Get cities by governorate (API)
     */
    public function getCities(Governorate $governorate)
    {
        $cities = $governorate->cities()
            ->withCount(['districts', 'lands'])
            ->orderBy('name')
            ->get();

        return response()->json($cities);
    }

    /**
     * Store a new city
     */
    public function storeCity(Request $request)
    {
        $request->validate([
            'governorate_id' => 'required|exists:governorates,id',
            'name' => 'required|string|max:255',
        ]);

        try {
            City::create($request->only(['governorate_id', 'name']));
            return redirect()->route('admin.geographic-areas.index')->with('success', 'تم إضافة المدينة بنجاح');
        } catch (\Exception $e) {
            Log::error('Store city error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إضافة المدينة')->withInput();
        }
    }

    /**
     * Update a city
     */
    public function updateCity(Request $request, City $city)
    {
        $request->validate([
            'governorate_id' => 'required|exists:governorates,id',
            'name' => 'required|string|max:255',
        ]);

        try {
            $city->update($request->only(['governorate_id', 'name']));
            return redirect()->route('admin.geographic-areas.index')->with('success', 'تم تحديث المدينة بنجاح');
        } catch (\Exception $e) {
            Log::error('Update city error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحديث المدينة');
        }
    }

    /**
     * Delete a city
     */
    public function destroyCity(City $city)
    {
        if ($city->districts()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف المدينة لأنها تحتوي على أحياء');
        }

        try {
            $city->delete();
            return redirect()->route('admin.geographic-areas.index')->with('success', 'تم حذف المدينة بنجاح');
        } catch (\Exception $e) {
            Log::error('Delete city error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف المدينة');
        }
    }

    // ==================== DISTRICTS ====================

    /**
     * Get districts by city (API)
     */
    public function getDistricts(City $city)
    {
        $districts = $city->districts()
            ->withCount(['zones', 'lands'])
            ->orderBy('name')
            ->get();

        return response()->json($districts);
    }

    /**
     * Store a new district
     */
    public function storeDistrict(Request $request)
    {
        $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255',
        ]);

        try {
            District::create($request->only(['city_id', 'name']));
            return redirect()->route('admin.geographic-areas.index')->with('success', 'تم إضافة الحي بنجاح');
        } catch (\Exception $e) {
            Log::error('Store district error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إضافة الحي')->withInput();
        }
    }

    /**
     * Update a district
     */
    public function updateDistrict(Request $request, District $district)
    {
        $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255',
        ]);

        try {
            $district->update($request->only(['city_id', 'name']));
            return redirect()->route('admin.geographic-areas.index')->with('success', 'تم تحديث الحي بنجاح');
        } catch (\Exception $e) {
            Log::error('Update district error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحديث الحي');
        }
    }

    /**
     * Delete a district
     */
    public function destroyDistrict(District $district)
    {
        if ($district->zones()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الحي لأنه يحتوي على مناطق');
        }

        try {
            $district->delete();
            return redirect()->route('admin.geographic-areas.index')->with('success', 'تم حذف الحي بنجاح');
        } catch (\Exception $e) {
            Log::error('Delete district error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف الحي');
        }
    }

    // ==================== ZONES ====================

    /**
     * Get zones by district (API)
     */
    public function getZones(District $district)
    {
        $zones = $district->zones()
            ->withCount(['areas', 'lands'])
            ->orderBy('name')
            ->get();

        return response()->json($zones);
    }

    /**
     * Store a new zone
     */
    public function storeZone(Request $request)
    {
        $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name' => 'required|string|max:255',
        ]);

        try {
            Zone::create($request->only(['district_id', 'name']));
            return redirect()->route('admin.geographic-areas.index')->with('success', 'تم إضافة المنطقة بنجاح');
        } catch (\Exception $e) {
            Log::error('Store zone error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إضافة المنطقة')->withInput();
        }
    }

    /**
     * Update a zone
     */
    public function updateZone(Request $request, Zone $zone)
    {
        $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name' => 'required|string|max:255',
        ]);

        try {
            $zone->update($request->only(['district_id', 'name']));
            return redirect()->route('admin.geographic-areas.index')->with('success', 'تم تحديث المنطقة بنجاح');
        } catch (\Exception $e) {
            Log::error('Update zone error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحديث المنطقة');
        }
    }

    /**
     * Delete a zone
     */
    public function destroyZone(Zone $zone)
    {
        if ($zone->areas()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف المنطقة لأنها تحتوي على أقسام');
        }

        try {
            $zone->delete();
            return redirect()->route('admin.geographic-areas.index')->with('success', 'تم حذف المنطقة بنجاح');
        } catch (\Exception $e) {
            Log::error('Delete zone error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف المنطقة');
        }
    }

    // ==================== AREAS ====================

    /**
     * Get areas by zone (API)
     */
    public function getAreas(Zone $zone)
    {
        $areas = $zone->areas()
            ->withCount('lands')
            ->orderBy('name')
            ->get();

        return response()->json($areas);
    }

    /**
     * Store a new area
     */
    public function storeArea(Request $request)
    {
        $request->validate([
            'zone_id' => 'required|exists:zones,id',
            'name' => 'required|string|max:255',
        ]);

        try {
            Area::create($request->only(['zone_id', 'name']));
            return redirect()->route('admin.geographic-areas.index')->with('success', 'تم إضافة القسم بنجاح');
        } catch (\Exception $e) {
            Log::error('Store area error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إضافة القسم')->withInput();
        }
    }

    /**
     * Update an area
     */
    public function updateArea(Request $request, Area $area)
    {
        $request->validate([
            'zone_id' => 'required|exists:zones,id',
            'name' => 'required|string|max:255',
        ]);

        try {
            $area->update($request->only(['zone_id', 'name']));
            return redirect()->route('admin.geographic-areas.index')->with('success', 'تم تحديث القسم بنجاح');
        } catch (\Exception $e) {
            Log::error('Update area error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحديث القسم');
        }
    }

    /**
     * Delete an area
     */
    public function destroyArea(Area $area)
    {
        if ($area->lands()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف القسم لأنه يحتوي على أراضي');
        }

        try {
            $area->delete();
            return redirect()->route('admin.geographic-areas.index')->with('success', 'تم حذف القسم بنجاح');
        } catch (\Exception $e) {
            Log::error('Delete area error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف القسم');
        }
    }
}
