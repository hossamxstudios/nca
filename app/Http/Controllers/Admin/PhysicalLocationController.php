<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Lane;
use App\Models\Stand;
use App\Models\Rack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PhysicalLocationController extends Controller
{
    /**
     * Display the physical locations hierarchy
     */
    public function index()
    {
        $rooms = Room::withCount('lanes')
            ->with(['lanes' => function ($q) {
                $q->withCount('stands')
                  ->with(['stands' => function ($q2) {
                      $q2->withCount('racks')
                        ->with(['racks' => function ($q3) {
                            $q3->withCount('files');
                        }]);
                  }]);
            }])
            ->orderBy('building_name')
            ->orderBy('name')
            ->get();

        $stats = [
            'rooms' => Room::count(),
            'lanes' => Lane::count(),
            'stands' => Stand::count(),
            'racks' => Rack::count(),
        ];

        return view('admin.physical-locations.index', compact('rooms', 'stats'));
    }

    // ==================== ROOMS ====================

    /**
     * Show room with full hierarchy (JSON)
     */
    public function showRoom(Room $room)
    {
        $room->load(['lanes' => function ($q) {
            $q->with(['stands' => function ($q2) {
                $q2->with('racks');
            }]);
        }]);

        return response()->json([
            'success' => true,
            'room' => $room
        ]);
    }

    /**
     * Store a new room
     */
    public function storeRoom(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'building_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            Room::create($request->only(['name', 'building_name', 'description']));
            return redirect()->route('admin.physical-locations.index')->with('success', 'تم إضافة الغرفة بنجاح');
        } catch (\Exception $e) {
            Log::error('Store room error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إضافة الغرفة')->withInput();
        }
    }

    /**
     * Update a room
     */
    public function updateRoom(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'building_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $room->update($request->only(['name', 'building_name', 'description']));
            return redirect()->route('admin.physical-locations.index')->with('success', 'تم تحديث الغرفة بنجاح');
        } catch (\Exception $e) {
            Log::error('Update room error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحديث الغرفة');
        }
    }

    /**
     * Delete a room
     */
    public function destroyRoom(Room $room)
    {
        if ($room->lanes()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الغرفة لأنها تحتوي على ممرات');
        }

        try {
            $room->delete();
            return redirect()->route('admin.physical-locations.index')->with('success', 'تم حذف الغرفة بنجاح');
        } catch (\Exception $e) {
            Log::error('Delete room error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف الغرفة');
        }
    }

    // ==================== LANES ====================

    /**
     * Store a new lane
     */
    public function storeLane(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            Lane::create($request->only(['room_id', 'name', 'description']));
            return redirect()->route('admin.physical-locations.index')->with('success', 'تم إضافة الممر بنجاح');
        } catch (\Exception $e) {
            Log::error('Store lane error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إضافة الممر')->withInput();
        }
    }

    /**
     * Update a lane
     */
    public function updateLane(Request $request, Lane $lane)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $lane->update($request->only(['room_id', 'name', 'description']));
            return redirect()->route('admin.physical-locations.index')->with('success', 'تم تحديث الممر بنجاح');
        } catch (\Exception $e) {
            Log::error('Update lane error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحديث الممر');
        }
    }

    /**
     * Delete a lane
     */
    public function destroyLane(Lane $lane)
    {
        if ($lane->stands()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الممر لأنه يحتوي على أرفف');
        }

        try {
            $lane->delete();
            return redirect()->route('admin.physical-locations.index')->with('success', 'تم حذف الممر بنجاح');
        } catch (\Exception $e) {
            Log::error('Delete lane error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف الممر');
        }
    }

    // ==================== STANDS ====================

    /**
     * Store a new stand
     */
    public function storeStand(Request $request)
    {
        $request->validate([
            'lane_id' => 'required|exists:lanes,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            Stand::create($request->only(['lane_id', 'name', 'description']));
            return redirect()->route('admin.physical-locations.index')->with('success', 'تم إضافة الحامل بنجاح');
        } catch (\Exception $e) {
            Log::error('Store stand error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إضافة الحامل')->withInput();
        }
    }

    /**
     * Update a stand
     */
    public function updateStand(Request $request, Stand $stand)
    {
        $request->validate([
            'lane_id' => 'required|exists:lanes,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $stand->update($request->only(['lane_id', 'name', 'description']));
            return redirect()->route('admin.physical-locations.index')->with('success', 'تم تحديث الحامل بنجاح');
        } catch (\Exception $e) {
            Log::error('Update stand error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحديث الحامل');
        }
    }

    /**
     * Delete a stand
     */
    public function destroyStand(Stand $stand)
    {
        if ($stand->racks()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الحامل لأنه يحتوي على أدراج');
        }

        try {
            $stand->delete();
            return redirect()->route('admin.physical-locations.index')->with('success', 'تم حذف الحامل بنجاح');
        } catch (\Exception $e) {
            Log::error('Delete stand error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف الحامل');
        }
    }

    // ==================== RACKS ====================

    /**
     * Store a new rack
     */
    public function storeRack(Request $request)
    {
        $request->validate([
            'stand_id' => 'required|exists:stands,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            Rack::create($request->only(['stand_id', 'name', 'description']));
            return redirect()->route('admin.physical-locations.index')->with('success', 'تم إضافة الدرج بنجاح');
        } catch (\Exception $e) {
            Log::error('Store rack error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إضافة الدرج')->withInput();
        }
    }

    /**
     * Update a rack
     */
    public function updateRack(Request $request, Rack $rack)
    {
        $request->validate([
            'stand_id' => 'required|exists:stands,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $rack->update($request->only(['stand_id', 'name', 'description']));
            return redirect()->route('admin.physical-locations.index')->with('success', 'تم تحديث الدرج بنجاح');
        } catch (\Exception $e) {
            Log::error('Update rack error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحديث الدرج');
        }
    }

    /**
     * Delete a rack
     */
    public function destroyRack(Rack $rack)
    {
        if ($rack->files()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الدرج لأنه يحتوي على ملفات');
        }

        try {
            $rack->delete();
            return redirect()->route('admin.physical-locations.index')->with('success', 'تم حذف الدرج بنجاح');
        } catch (\Exception $e) {
            Log::error('Delete rack error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف الدرج');
        }
    }
}
