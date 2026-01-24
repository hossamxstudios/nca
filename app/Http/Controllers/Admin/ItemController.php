<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ItemController extends Controller
{
    /**
     * Display items list
     */
    public function index()
    {
        $items = Item::withCount('files')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $stats = [
            'total' => Item::count(),
            'with_files' => Item::has('files')->count(),
            'without_files' => Item::doesntHave('files')->count(),
        ];

        return view('admin.items.index', compact('items', 'stats'));
    }

    /**
     * Store a new item
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:items,name',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
        ]);

        try {
            $item = Item::create([
                'name' => $request->name,
                'description' => $request->description,
                'order' => $request->order ?? 0,
            ]);

            ActivityLogger::created($item, ActivityLog::GROUP_SETTINGS, ['name' => $item->name]);

            return redirect()->route('admin.items.index')->with('success', 'تم إضافة نوع المحتوى بنجاح');
        } catch (\Exception $e) {
            Log::error('Store item error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إضافة نوع المحتوى')->withInput();
        }
    }

    /**
     * Update an item
     */
    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:items,name,' . $item->id,
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
        ]);

        try {
            $oldValues = $item->only(['name']);
            $item->update([
                'name' => $request->name,
                'description' => $request->description,
                'order' => $request->order ?? 0,
            ]);
            $newValues = $item->only(['name']);

            ActivityLogger::updated($item, ActivityLog::GROUP_SETTINGS, $oldValues, $newValues);

            return redirect()->route('admin.items.index')->with('success', 'تم تحديث نوع المحتوى بنجاح');
        } catch (\Exception $e) {
            Log::error('Update item error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحديث نوع المحتوى');
        }
    }

    /**
     * Delete an item
     */
    public function destroy(Item $item)
    {
        if ($item->files()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف نوع المحتوى لأنه مرتبط بملفات');
        }

        try {
            ActivityLogger::deleted($item, ActivityLog::GROUP_SETTINGS);

            $item->delete();
            return redirect()->route('admin.items.index')->with('success', 'تم حذف نوع المحتوى بنجاح');
        } catch (\Exception $e) {
            Log::error('Delete item error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف نوع المحتوى');
        }
    }

    /**
     * Update items order
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:items,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        try {
            foreach ($request->items as $itemData) {
                Item::where('id', $itemData['id'])->update(['order' => $itemData['order']]);
            }

            ActivityLogger::make()
                ->action(ActivityLog::ACTION_UPDATE, ActivityLog::GROUP_SETTINGS)
                ->description("تحديث ترتيب أنواع المحتوى (" . count($request->items) . " عنصر)")
                ->log();

            return response()->json(['success' => true, 'message' => 'تم تحديث الترتيب بنجاح']);
        } catch (\Exception $e) {
            Log::error('Update items order error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'حدث خطأ أثناء تحديث الترتيب'], 500);
        }
    }
}
