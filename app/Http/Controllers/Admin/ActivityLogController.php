<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        // Filter by user
        if ($request->filled('user_id')) {
            $query->byUser($request->user_id);
        }

        // Filter by action type
        if ($request->filled('action_type')) {
            $query->byActionType($request->action_type);
        }

        // Filter by action group
        if ($request->filled('action_group')) {
            $query->byActionGroup($request->action_group);
        }

        // Filter by date range
        if ($request->filled('date_from') || $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $logs = $query->paginate(50)->withQueryString();

        // Get filter options
        $users = User::orderBy('first_name')->get();

        $actionTypes = [
            ActivityLog::ACTION_LOGIN => 'تسجيل دخول',
            ActivityLog::ACTION_LOGOUT => 'تسجيل خروج',
            ActivityLog::ACTION_VIEW => 'عرض',
            ActivityLog::ACTION_CREATE => 'إنشاء',
            ActivityLog::ACTION_UPDATE => 'تعديل',
            ActivityLog::ACTION_DELETE => 'حذف',
            ActivityLog::ACTION_PRINT => 'طباعة',
            ActivityLog::ACTION_BULK_IMPORT => 'استيراد جماعي',
            ActivityLog::ACTION_BULK_DELETE => 'حذف جماعي',
            ActivityLog::ACTION_EXPORT => 'تصدير',
        ];

        $actionGroups = [
            ActivityLog::GROUP_AUTH => 'المصادقة',
            ActivityLog::GROUP_CLIENTS => 'العملاء',
            ActivityLog::GROUP_FILES => 'الملفات',
            ActivityLog::GROUP_USERS => 'المستخدمين',
            ActivityLog::GROUP_ROLES => 'الصلاحيات',
            ActivityLog::GROUP_SETTINGS => 'الإعدادات',
            ActivityLog::GROUP_IMPORTS => 'الاستيراد',
            ActivityLog::GROUP_GEOGRAPHIC => 'المواقع الجغرافية',
            ActivityLog::GROUP_PHYSICAL => 'المواقع الفعلية',
        ];

        // Stats
        $stats = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'this_week' => ActivityLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'logins_today' => ActivityLog::where('action_type', ActivityLog::ACTION_LOGIN)->whereDate('created_at', today())->count(),
        ];

        return view('admin.activity-logs.index', compact(
            'logs',
            'users',
            'actionTypes',
            'actionGroups',
            'stats'
        ));
    }

    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('user');

        return response()->json([
            'id' => $activityLog->id,
            'user' => $activityLog->user?->name ?? 'النظام',
            'action_type' => $activityLog->action_type_label,
            'action_type_color' => $activityLog->action_type_color,
            'action_type_icon' => $activityLog->action_type_icon,
            'action_group' => $activityLog->action_group_label,
            'subject_name' => $activityLog->subject_name,
            'description' => $activityLog->description,
            'properties' => $activityLog->properties,
            'old_values' => $activityLog->old_values,
            'new_values' => $activityLog->new_values,
            'affected_ids' => $activityLog->affected_ids,
            'batch_id' => $activityLog->batch_id,
            'batch_count' => $activityLog->batch_count,
            'ip_address' => $activityLog->ip_address,
            'user_agent' => $activityLog->user_agent,
            'created_at' => $activityLog->created_at->format('Y-m-d H:i:s'),
            'created_at_human' => $activityLog->created_at->diffForHumans(),
        ]);
    }

    public function userTimeline(User $user)
    {
        $logs = ActivityLog::byUser($user->id)
            ->latest()
            ->paginate(50);

        return view('admin.activity-logs.user-timeline', compact('user', 'logs'));
    }

    public function destroy(ActivityLog $activityLog)
    {
        $activityLog->delete();

        return redirect()->back()->with('success', 'تم حذف السجل بنجاح');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'log_ids' => 'required|array|min:1',
            'log_ids.*' => 'exists:activity_logs,id',
        ]);

        ActivityLog::whereIn('id', $request->log_ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف السجلات المحددة بنجاح'
        ]);
    }

    public function clearOld(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:7',
        ]);

        $count = ActivityLog::where('created_at', '<', now()->subDays($request->days))->delete();

        return redirect()->back()->with('success', "تم حذف {$count} سجل قديم");
    }
}
