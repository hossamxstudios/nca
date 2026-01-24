<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Lane;
use App\Models\Stand;
use App\Models\Rack;
use App\Models\ActivityLog;
use App\Services\ActivityLogger;
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
            $room = Room::create($request->only(['name', 'building_name', 'description']));

            ActivityLogger::created($room, ActivityLog::GROUP_PHYSICAL, ['name' => $room->name]);

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
            $oldValues = $room->only(['name', 'building_name']);
            $room->update($request->only(['name', 'building_name', 'description']));
            $newValues = $room->only(['name', 'building_name']);

            ActivityLogger::updated($room, ActivityLog::GROUP_PHYSICAL, $oldValues, $newValues);

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
            ActivityLogger::deleted($room, ActivityLog::GROUP_PHYSICAL);

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
            $lane = Lane::create($request->only(['room_id', 'name', 'description']));

            ActivityLogger::created($lane, ActivityLog::GROUP_PHYSICAL, ['name' => $lane->name]);

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
            $oldValues = $lane->only(['name']);
            $lane->update($request->only(['room_id', 'name', 'description']));
            $newValues = $lane->only(['name']);

            ActivityLogger::updated($lane, ActivityLog::GROUP_PHYSICAL, $oldValues, $newValues);

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
            ActivityLogger::deleted($lane, ActivityLog::GROUP_PHYSICAL);

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
            $stand = Stand::create($request->only(['lane_id', 'name', 'description']));

            ActivityLogger::created($stand, ActivityLog::GROUP_PHYSICAL, ['name' => $stand->name]);

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
            $oldValues = $stand->only(['name']);
            $stand->update($request->only(['lane_id', 'name', 'description']));
            $newValues = $stand->only(['name']);

            ActivityLogger::updated($stand, ActivityLog::GROUP_PHYSICAL, $oldValues, $newValues);

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
            ActivityLogger::deleted($stand, ActivityLog::GROUP_PHYSICAL);

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
            $rack = Rack::create($request->only(['stand_id', 'name', 'description']));

            ActivityLogger::created($rack, ActivityLog::GROUP_PHYSICAL, ['name' => $rack->name]);

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
            $oldValues = $rack->only(['name']);
            $rack->update($request->only(['stand_id', 'name', 'description']));
            $newValues = $rack->only(['name']);

            ActivityLogger::updated($rack, ActivityLog::GROUP_PHYSICAL, $oldValues, $newValues);

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
            ActivityLogger::deleted($rack, ActivityLog::GROUP_PHYSICAL);

            $rack->delete();
            return redirect()->route('admin.physical-locations.index')->with('success', 'تم حذف الدرج بنجاح');
        } catch (\Exception $e) {
            Log::error('Delete rack error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف الدرج');
        }
    }
}
