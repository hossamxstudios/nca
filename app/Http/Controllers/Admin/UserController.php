<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles']);

        if ($request->input('trashed') === 'only') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $roles = Role::all();

        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $trashedUsers = User::onlyTrashed()->count();

        return view('admin.users.index', compact('users', 'roles', 'totalUsers', 'activeUsers', 'trashedUsers'));
    }

    public function show($id)
    {
        $user = User::with(['roles'])->withTrashed()->findOrFail($id);
        $roles = Role::all();
        return view('admin.users.show', compact('user', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'job_title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'role' => 'nullable|exists:roles,name',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'job_title' => $request->job_title,
                'department' => $request->department,
                'is_active' => $request->boolean('is_active', true),
            ]);

            if ($request->filled('role')) {
                $user->syncRoles([$request->role]);
            }

            if ($request->hasFile('avatar')) {
                $user->addMediaFromRequest('avatar')->toMediaCollection('avatar');
            }

            DB::commit();
            return redirect()->route('admin.users.index')->with('success', 'تم إضافة المستخدم بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User store error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إضافة المستخدم')->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'job_title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'role' => 'nullable|exists:roles,name',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'job_title' => $request->job_title,
                'department' => $request->department,
                'is_active' => $request->boolean('is_active', true),
            ]);

            if ($request->filled('role')) {
                $user->syncRoles([$request->role]);
            }

            if ($request->hasFile('avatar')) {
                $user->clearMediaCollection('avatar');
                $user->addMediaFromRequest('avatar')->toMediaCollection('avatar');
            }

            DB::commit();
            return redirect()->route('admin.users.index')->with('success', 'تم تحديث بيانات المستخدم بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User update error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحديث بيانات المستخدم')->withInput();
        }
    }

    public function destroy($id)
    {
        if ($id == auth()->id()) {
            return redirect()->back()->with('error', 'لا يمكنك حذف حسابك الخاص');
        }

        DB::beginTransaction();
        try {
            User::findOrFail($id)->delete();
            DB::commit();
            return redirect()->route('admin.users.index')->with('success', 'تم حذف المستخدم بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User destroy error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف المستخدم');
        }
    }

    public function restore($id)
    {
        DB::beginTransaction();
        try {
            User::withTrashed()->findOrFail($id)->restore();
            DB::commit();
            return redirect()->back()->with('success', 'تم استعادة المستخدم بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User restore error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء استعادة المستخدم');
        }
    }

    public function forceDelete($id)
    {
        if ($id == auth()->id()) {
            return redirect()->back()->with('error', 'لا يمكنك حذف حسابك الخاص');
        }

        DB::beginTransaction();
        try {
            $user = User::withTrashed()->findOrFail($id);
            $user->clearMediaCollection('avatar');
            $user->forceDelete();
            DB::commit();
            return redirect()->back()->with('success', 'تم حذف المستخدم نهائياً');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User forceDelete error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء الحذف النهائي');
        }
    }

    public function toggleStatus($id)
    {
        if ($id == auth()->id()) {
            return response()->json(['success' => false, 'message' => 'لا يمكنك تعطيل حسابك الخاص'], 403);
        }

        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->update(['is_active' => !$user->is_active]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => $user->is_active ? 'تم تفعيل المستخدم' : 'تم تعطيل المستخدم',
                'is_active' => $user->is_active,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User toggleStatus error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ'], 500);
        }
    }

    public function assignRole(Request $request, $id)
    {
        $request->validate(['role' => 'required|exists:roles,name']);

        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->syncRoles([$request->role]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم تعيين الصلاحية بنجاح',
                'role' => $request->role,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User assignRole error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ'], 500);
        }
    }

    public function changePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->password = Hash::make($request->password);
            $user->save();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'تم تغيير كلمة المرور بنجاح']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Password change error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء تغيير كلمة المرور'], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array|min:1', 'ids.*' => 'exists:users,id']);

        $ids = array_filter($request->ids, fn($id) => $id != auth()->id());
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'لا يوجد مستخدمين للحذف'], 400);
        }

        DB::beginTransaction();
        try {
            $count = User::whereIn('id', $ids)->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => "تم حذف {$count} مستخدم بنجاح"]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk delete error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء الحذف'], 500);
        }
    }

    public function bulkRestore(Request $request)
    {
        $request->validate(['ids' => 'required|array|min:1']);

        DB::beginTransaction();
        try {
            $count = User::onlyTrashed()->whereIn('id', $request->ids)->restore();
            DB::commit();
            return response()->json(['success' => true, 'message' => "تم استعادة {$count} مستخدم بنجاح"]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk restore error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء الاستعادة'], 500);
        }
    }

    public function bulkForceDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array|min:1']);

        $ids = array_filter($request->ids, fn($id) => $id != auth()->id());
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'لا يوجد مستخدمين للحذف'], 400);
        }

        DB::beginTransaction();
        try {
            $count = User::onlyTrashed()->whereIn('id', $ids)->forceDelete();
            DB::commit();
            return response()->json(['success' => true, 'message' => "تم حذف {$count} مستخدم نهائياً"]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk force delete error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء الحذف'], 500);
        }
    }

    public function bulkUpload(Request $request)
    {
        // TODO: Implement bulk upload functionality
        return response()->json(['success' => false, 'message' => 'غير متاح حالياً'], 501);
    }

    public function bulkDownload(Request $request)
    {
        // TODO: Implement bulk download functionality
        return response()->json(['success' => false, 'message' => 'غير متاح حالياً'], 501);
    }

    public function downloadSample()
    {
        // TODO: Implement sample download functionality
        return response()->json(['success' => false, 'message' => 'غير متاح حالياً'], 501);
    }
}
