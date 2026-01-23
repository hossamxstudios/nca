<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $user->load('roles', 'permissions');

        return view('admin.profile.index', [
            'user' => $user,
        ]);
    }

    public function edit(Request $request): View
    {
        $user = $request->user();
        $user->load('roles', 'permissions');

        return view('admin.profile.index', [
            'user' => $user,
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $user = $request->user();
            $user->fill($request->validated());

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $user->clearMediaCollection('avatar');
                $user->addMediaFromRequest('avatar')
                    ->toMediaCollection('avatar');
            }

            DB::commit();

            return Redirect::route('admin.profile.index')
                ->with('success', 'تم تحديث الملف الشخصي بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ProfileController@update: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحديث الملف الشخصي');
        }
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', Password::defaults(), 'confirmed'],
            ], [
                'current_password.required' => 'كلمة المرور الحالية مطلوبة',
                'current_password.current_password' => 'كلمة المرور الحالية غير صحيحة',
                'password.required' => 'كلمة المرور الجديدة مطلوبة',
                'password.confirmed' => 'تأكيد كلمة المرور غير مطابق',
            ]);

            DB::beginTransaction();

            $request->user()->update([
                'password' => Hash::make($validated['password']),
            ]);

            DB::commit();

            return Redirect::route('admin.profile.index')
                ->with('success', 'تم تغيير كلمة المرور بنجاح');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ProfileController@updatePassword: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تغيير كلمة المرور');
        }
    }

    public function removeAvatar(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $request->user()->clearMediaCollection('avatar');
            DB::commit();

            return Redirect::route('admin.profile.index')
                ->with('success', 'تم حذف الصورة الشخصية بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ProfileController@removeAvatar: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف الصورة');
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
