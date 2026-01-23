<div class="card">
    {{-- Bulk Actions Bar (hidden by default) --}}
    <div id="bulkActionsBar" class="m-3 mb-0 alert alert-primary d-none">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="ti ti-checkbox me-2"></i>
                <span id="selectedCount">0</span> مستخدم محدد
            </div>
            <div class="gap-2 d-flex">
                @can('users.delete')
                <button type="button" class="btn btn-sm btn-danger" onclick="bulkDelete()">
                    <i class="ti ti-trash me-1"></i>حذف المحدد
                </button>
                @endcan
                <button type="button" class="btn btn-sm btn-secondary" onclick="clearSelection()">
                    <i class="ti ti-x me-1"></i>إلغاء التحديد
                </button>
            </div>
        </div>
    </div>

    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 card-title">
            <i class="ti ti-table me-1"></i>قائمة المستخدمين
        </h5>
        <span class="badge bg-primary">{{ $users->total() }} مستخدم</span>
    </div>
    <div class="p-0 card-body">
        @if($users->count() > 0)
        <div class="table-responsive">
            <table class="table mb-0 table-hover table-striped">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        <th>المستخدم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الهاتف</th>
                        <th>الصلاحية</th>
                        <th class="text-center">الحالة</th>
                        <th>آخر دخول</th>
                        <th class="text-center" style="width: 180px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input user-checkbox" value="{{ $user->id }}">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle me-2" width="40" height="40">
                                @else
                                    <div class="avatar-sm me-2">
                                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                            {{ $user->initials }}
                                        </span>
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    @if($user->job_title)
                                        <small class="text-muted">{{ $user->job_title }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-dark">{{ $user->email }}</span>
                        </td>
                        <td>
                            @if($user->phone)
                                <span class="text-dark">{{ $user->phone }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @forelse($user->roles as $role)
                                <span class="badge bg-info-subtle text-info">{{ $role->name }}</span>
                            @empty
                                <span class="text-muted">-</span>
                            @endforelse
                        </td>
                        <td class="text-center">
                            @if($user->trashed())
                                <span class="badge bg-secondary-subtle text-secondary">محذوف</span>
                            @elseif($user->is_active)
                                <span class="badge bg-success-subtle text-success">نشط</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">غير نشط</span>
                            @endif
                        </td>
                        <td>
                            @if($user->last_login_at)
                                <small class="text-muted">{{ $user->last_login_at->diffForHumans() }}</small>
                            @else
                                <small class="text-muted">لم يسجل دخول</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="gap-1 d-flex justify-content-center">
                                @if($user->trashed())
                                    <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="px-2 btn btn-icon btn-sm bg-success-subtle text-success" title="استعادة">
                                            <i class="ti ti-restore fs-5"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.users.force-delete', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="px-2 btn btn-icon btn-sm bg-danger-subtle text-danger" title="حذف نهائي" onclick="return confirm('هل أنت متأكد من الحذف النهائي؟')">
                                            <i class="ti ti-trash-x fs-5"></i>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="px-2 btn btn-icon btn-sm bg-info-subtle text-info" title="عرض">
                                        <i class="ti ti-eye fs-5"></i>
                                    </a>
                                    @can('users.edit')
                                    <button type="button" class="px-2 btn btn-icon btn-sm bg-warning-subtle text-warning" title="تعديل" data-bs-toggle="modal" data-bs-target="#editUserModal_{{ $user->id }}">
                                        <i class="ti ti-edit fs-5"></i>
                                    </button>
                                    <button type="button" class="px-2 btn btn-icon btn-sm bg-primary-subtle text-primary" title="تغيير كلمة المرور" data-bs-toggle="modal" data-bs-target="#changePasswordModal_{{ $user->id }}">
                                        <i class="ti ti-key fs-5"></i>
                                    </button>
                                    @endcan
                                    @can('users.delete')
                                    @if($user->id != auth()->id())
                                    <button type="button" class="px-2 btn btn-icon btn-sm bg-danger-subtle text-danger" title="حذف" data-bs-toggle="modal" data-bs-target="#deleteUserModal_{{ $user->id }}">
                                        <i class="ti ti-trash fs-5"></i>
                                    </button>
                                    @endif
                                    @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="py-5 text-center">
            <div class="mx-auto mb-3 avatar-lg">
                <span class="avatar-title bg-light text-muted rounded-circle fs-1">
                    <i class="ti ti-users-minus"></i>
                </span>
            </div>
            <h5 class="text-muted">لا يوجد مستخدمين</h5>
            <p class="mb-3 text-muted">لم يتم العثور على أي مستخدمين مطابقين لمعايير البحث</p>
            @can('users.create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="ti ti-plus me-1"></i>إضافة مستخدم جديد
            </button>
            @endcan
        </div>
        @endif
    </div>
    @if($users->hasPages())
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted fs-sm">
                عرض {{ $users->firstItem() }} إلى {{ $users->lastItem() }} من {{ $users->total() }} مستخدم
            </div>
            {{ $users->links() }}
        </div>
    </div>
    @endif
</div>
