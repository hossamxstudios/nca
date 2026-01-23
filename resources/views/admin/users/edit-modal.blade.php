{{-- Edit User Modals - One modal per user --}}
@foreach($users as $user)
@if(!$user->trashed())
<div class="modal fade" id="editUserModal_{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel_{{ $user->id }}">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel_{{ $user->id }}">
                        <i class="ti ti-user-edit me-2"></i>تعديل بيانات: {{ $user->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">الاسم الأول <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" value="{{ $user->first_name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الاسم الأخير <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" value="{{ $user->last_name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="text" name="phone" class="form-control" value="{{ $user->phone }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">المسمى الوظيفي</label>
                            <input type="text" name="job_title" class="form-control" value="{{ $user->job_title }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">القسم</label>
                            <input type="text" name="department" class="form-control" value="{{ $user->department }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الصلاحية</label>
                            <select name="role" class="form-select">
                                <option value="">-- اختر الصلاحية --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الحالة</label>
                            <div class="mt-2 form-check form-switch">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="editUserActive_{{ $user->id }}" {{ $user->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="editUserActive_{{ $user->id }}">نشط</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">الصورة الشخصية</label>
                            @if($user->avatar_url)
                                <div class="mb-2">
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded" width="60" height="60">
                                </div>
                            @endif
                            <input type="file" name="avatar" class="form-control" accept="image/*">
                            <small class="text-muted">اتركه فارغاً للحفاظ على الصورة الحالية</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>إلغاء
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i>حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach
