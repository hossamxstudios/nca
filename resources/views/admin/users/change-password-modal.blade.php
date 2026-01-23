{{-- Change Password Modals - One modal per user --}}
@foreach($users as $user)
@if(!$user->trashed())
<div class="modal fade" id="changePasswordModal_{{ $user->id }}" tabindex="-1" aria-labelledby="changePasswordModalLabel_{{ $user->id }}">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel_{{ $user->id }}">
                    <i class="ti ti-key me-2"></i>تغيير كلمة المرور
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="changePasswordForm_{{ $user->id }}" onsubmit="changePassword(event, {{ $user->id }})">
                <div class="modal-body">
                    <div class="mb-3 text-center">
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="mb-2 rounded-circle" width="60" height="60">
                        @else
                            <div class="mx-auto mb-2 avatar-md">
                                <span class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                    {{ $user->initials }}
                                </span>
                            </div>
                        @endif
                        <h6 class="mb-0">{{ $user->name }}</h6>
                        <small class="text-muted">{{ $user->email }}</small>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label">كلمة المرور الجديدة <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                        <small class="text-muted">يجب أن تكون 8 أحرف على الأقل</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <div class="mb-0 alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        سيتم تسجيل خروج المستخدم من جميع الجلسات النشطة.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>إلغاء
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i>تغيير كلمة المرور
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach
