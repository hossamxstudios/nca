<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Module translations (Arabic)
     */
    private array $moduleTranslations = [
        'clients' => 'العملاء',
        'lands' => 'الأراضي',
        'files' => 'الملفات',
        'users' => 'المستخدمين',
        'roles' => 'الصلاحيات',
        'physical_locations' => 'مواقع التخزين',
        'geographic_areas' => 'المناطق الجغرافية',
        'items' => 'أنواع المحتوى',
        'import' => 'الاستيراد',
        'reports' => 'التقارير',
        'settings' => 'الإعدادات',
        'general' => 'عام',
    ];

    /**
     * Action translations (Arabic)
     */
    private array $actionTranslations = [
        'view' => 'عرض',
        'create' => 'إضافة',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'restore' => 'استعادة',
        'force-delete' => 'حذف نهائي',
        'bulk-delete' => 'حذف جماعي',
        'bulk-restore' => 'استعادة جماعية',
        'bulk-force-delete' => 'حذف نهائي جماعي',
        'bulk-print' => 'طباعة جماعية',
        'upload' => 'رفع',
        'download' => 'تحميل',
        'download-template' => 'تحميل القالب',
        'export' => 'تصدير',
        'print' => 'طباعة',
        'access' => 'الوصول',
        'execute' => 'تنفيذ',
        'toggle-status' => 'تغيير الحالة',
        'assign-role' => 'تعيين صلاحية',
        'change-password' => 'تغيير كلمة المرور',
        'sync-permissions' => 'مزامنة الأذونات',
    ];

    /**
     * Display a listing of roles
     */
    public function index(Request $request)
    {
        $query = Role::with('permissions')->withCount('users');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $roles = $query->latest()->paginate(15)->withQueryString();

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        $allPermissions = Permission::orderBy('name')->get();
        $permissions = $this->groupPermissionsByModule($allPermissions);
        $moduleTranslations = $this->moduleTranslations;
        $actionTranslations = $this->actionTranslations;

        return view('admin.roles.create', compact('permissions', 'moduleTranslations', 'actionTranslations'));
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        try {
            DB::beginTransaction();

            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'web',
            ]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            DB::commit();

            return redirect()->route('admin.roles.index')->with('success', 'تم إنشاء الصلاحية بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role store error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إنشاء الصلاحية')->withInput();
        }
    }

    /**
     * Display the specified role
     */
    public function show($id)
    {
        $role = Role::with(['permissions', 'users'])->findOrFail($id);
        $allPermissions = Permission::orderBy('name')->get();
        $groupedPermissions = $this->groupPermissionsByModule($allPermissions);
        $moduleTranslations = $this->moduleTranslations;
        $actionTranslations = $this->actionTranslations;

        return view('admin.roles.show', compact('role', 'groupedPermissions', 'moduleTranslations', 'actionTranslations'));
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $allPermissions = Permission::orderBy('name')->get();
        $permissions = $this->groupPermissionsByModule($allPermissions);
        $moduleTranslations = $this->moduleTranslations;
        $actionTranslations = $this->actionTranslations;

        return view('admin.roles.edit', compact('role', 'permissions', 'moduleTranslations', 'actionTranslations'));
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        try {
            DB::beginTransaction();

            if (in_array($role->name, ['super-admin', 'admin']) && $role->name !== $request->name) {
                return back()->with('error', 'لا يمكن تغيير اسم صلاحيات النظام الأساسية');
            }

            $role->update(['name' => $request->name]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            } else {
                $role->syncPermissions([]);
            }

            DB::commit();

            return redirect()->route('admin.roles.index')->with('success', 'تم تحديث الصلاحية بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role update error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحديث الصلاحية')->withInput();
        }
    }

    /**
     * Remove the specified role
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if (in_array($role->name, ['super-admin', 'admin'])) {
            return back()->with('error', 'لا يمكن حذف صلاحيات النظام الأساسية');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف صلاحية مرتبطة بمستخدمين');
        }

        try {
            $role->delete();
            return redirect()->route('admin.roles.index')->with('success', 'تم حذف الصلاحية بنجاح');
        } catch (\Exception $e) {
            Log::error('Role destroy error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف الصلاحية');
        }
    }

    /**
     * Bulk delete roles
     */
    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:roles,id']);

        try {
            $protectedRoles = Role::whereIn('name', ['super-admin', 'admin'])->pluck('id')->toArray();
            $ids = array_diff($request->ids ?? [], $protectedRoles);

            if (empty($ids)) {
                return response()->json(['success' => false, 'message' => 'لا توجد صلاحيات صالحة للحذف'], 400);
            }

            $rolesWithUsers = Role::whereIn('id', $ids)->whereHas('users')->pluck('id')->toArray();
            $ids = array_diff($ids, $rolesWithUsers);

            if (empty($ids)) {
                return response()->json(['success' => false, 'message' => 'الصلاحيات المحددة مرتبطة بمستخدمين'], 400);
            }

            Role::whereIn('id', $ids)->delete();

            return response()->json(['success' => true, 'message' => 'تم حذف ' . count($ids) . ' صلاحية بنجاح']);
        } catch (\Exception $e) {
            Log::error('Role bulkDelete error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء الحذف'], 500);
        }
    }

    /**
     * Sync permissions for a role (AJAX)
     */
    public function syncPermissions(Request $request, $id)
    {
        $request->validate(['permissions' => 'array']);

        try {
            $role = Role::findOrFail($id);
            $role->syncPermissions($request->permissions ?? []);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الأذونات بنجاح',
                'permissions' => $role->permissions->pluck('name'),
            ]);
        } catch (\Exception $e) {
            Log::error('Role syncPermissions error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ'], 500);
        }
    }

    /**
     * Group permissions by module name
     */
    private function groupPermissionsByModule(Collection $permissions): Collection
    {
        return $permissions->groupBy(function (Permission $permission) {
            return $this->extractModuleName($permission->name);
        })->sortKeys();
    }

    /**
     * Extract module name from permission name
     * Supports dot notation: "clients.view" -> "clients"
     * Also supports dash notation: "view-clients" -> "clients"
     */
    private function extractModuleName(string $permissionName): string
    {
        // Dot notation: "clients.view" -> "clients"
        if (str_contains($permissionName, '.')) {
            $parts = explode('.', $permissionName);
            return $parts[0] ?: 'general';
        }

        // Dash notation fallback: "view-clients" -> "clients"
        $prefixes = [
            'bulk-force-delete-', 'force-delete-', 'bulk-upload-', 'bulk-download-',
            'bulk-delete-', 'bulk-restore-', 'create-', 'view-', 'edit-', 'delete-',
            'restore-', 'update-', 'approve-', 'reject-', 'cancel-', 'process-', 'print-',
        ];

        foreach ($prefixes as $prefix) {
            if (str_starts_with($permissionName, $prefix)) {
                return substr($permissionName, strlen($prefix)) ?: 'general';
            }
        }

        return 'general';
    }
}
