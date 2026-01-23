<!DOCTYPE html>
@include('admin.main.html')

<head>
    <title>{{ $user->name }} - أرشيف القاهرة الجديدة</title>
    @include('admin.main.meta')
</head>

<body>
    <div class="wrapper">
        @include('admin.main.topbar')
        @include('admin.main.sidebar')
        <div class="content-page">
            <div class="container-fluid">
                {{-- Header --}}
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-flex align-items-center justify-content-between">
                            <h4 class="mb-0">
                                <i class="ti ti-user me-2"></i>بيانات المستخدم
                            </h4>
                            <div class="page-title-right">
                                <ol class="m-0 breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">المستخدمين</a></li>
                                    <li class="breadcrumb-item active">{{ $user->name }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- User Profile Card --}}
                    <div class="col-lg-4">
                        <div class="border-0 shadow card">
                            <div class="py-4 text-center card-body">
                                @if($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="mb-3 shadow-sm rounded-circle" width="120" height="120">
                                @else
                                    <div class="mx-auto mb-3 avatar-xl">
                                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-1">
                                            {{ $user->initials }}
                                        </span>
                                    </div>
                                @endif
                                <h4 class="mb-1 fw-semibold">{{ $user->name }}</h4>
                                @if($user->job_title)
                                    <p class="mb-2 text-muted">{{ $user->job_title }}</p>
                                @endif
                                <div class="mb-3">
                                    @forelse($user->roles as $role)
                                        <span class="badge bg-info-subtle text-info fs-6">{{ $role->name }}</span>
                                    @empty
                                        <span class="text-muted">بدون صلاحية</span>
                                    @endforelse
                                </div>
                                @if($user->trashed())
                                    <span class="px-3 py-2 badge bg-secondary-subtle text-secondary">محذوف</span>
                                @elseif($user->is_active)
                                    <span class="px-3 py-2 badge bg-success-subtle text-success">نشط</span>
                                @else
                                    <span class="px-3 py-2 badge bg-danger-subtle text-danger">غير نشط</span>
                                @endif
                            </div>
                            <div class="pt-0 card-body">
                                <hr class="my-3">
                                <div class="mb-3 d-flex align-items-center">
                                    <div class="avatar-xs me-3">
                                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                            <i class="ti ti-mail"></i>
                                        </span>
                                    </div>
                                    <span class="text-dark">{{ $user->email }}</span>
                                </div>
                                @if($user->phone)
                                <div class="mb-3 d-flex align-items-center">
                                    <div class="avatar-xs me-3">
                                        <span class="avatar-title bg-success-subtle text-success rounded-circle">
                                            <i class="ti ti-phone"></i>
                                        </span>
                                    </div>
                                    <span class="text-dark">{{ $user->phone }}</span>
                                </div>
                                @endif
                                @if($user->department)
                                <div class="mb-3 d-flex align-items-center">
                                    <div class="avatar-xs me-3">
                                        <span class="avatar-title bg-warning-subtle text-warning rounded-circle">
                                            <i class="ti ti-building"></i>
                                        </span>
                                    </div>
                                    <span class="text-dark">{{ $user->department }}</span>
                                </div>
                                @endif
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs me-3">
                                        <span class="avatar-title bg-info-subtle text-info rounded-circle">
                                            <i class="ti ti-calendar"></i>
                                        </span>
                                    </div>
                                    <span class="text-dark">انضم {{ $user->created_at->format('Y/m/d') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Actions Card --}}
                        @if(!$user->trashed() && $user->id != auth()->id())
                        <div class="border-0 shadow card">
                            <div class="card-header">
                                <h5 class="mb-0 card-title">
                                    <i class="ti ti-settings me-2"></i>الإجراءات
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="gap-2 d-grid">
                                    @can('users.edit')
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserShowModal">
                                        <i class="ti ti-edit me-1"></i>تعديل البيانات
                                    </button>
                                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#changePasswordShowModal">
                                        <i class="ti ti-key me-1"></i>تغيير كلمة المرور
                                    </button>
                                    {{-- <button type="button" class="btn btn-outline-{{ $user->is_active ? 'secondary' : 'success' }}" onclick="toggleUserStatus({{ $user->id }})">
                                        <i class="ti ti-{{ $user->is_active ? 'user-off' : 'user-check' }} me-1"></i>
                                        {{ $user->is_active ? 'تعطيل الحساب' : 'تفعيل الحساب' }}
                                    </button> --}}
                                    @endcan
                                    @can('users.delete')
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUserShowModal">
                                        <i class="ti ti-trash me-1"></i>حذف المستخدم
                                    </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($user->trashed())
                        <div class="border-0 shadow card">
                            <div class="card-header bg-warning-subtle">
                                <h5 class="mb-0 card-title text-warning">
                                    <i class="ti ti-alert-triangle me-2"></i>مستخدم محذوف
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="gap-2 d-grid">
                                    <form action="{{ route('admin.users.restore', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="ti ti-restore me-1"></i>استعادة المستخدم
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.users.force-delete', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('هل أنت متأكد من الحذف النهائي؟ لا يمكن التراجع عن هذا الإجراء.')">
                                            <i class="ti ti-trash-x me-1"></i>حذف نهائي
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- User Details --}}
                    <div class="col-lg-8">
                        <div class="border-0 shadow card">
                            <div class="card-header">
                                <h5 class="mb-0 card-title">
                                    <i class="ti ti-info-circle me-2"></i>معلومات تفصيلية
                                </h5>
                            </div>
                            <div class="p-0 card-body">
                                <div class="table-responsive">
                                    <table class="table mb-0 table-striped">
                                        <tbody>
                                            <tr>
                                                <th class="text-muted ps-4" style="width: 200px;">الاسم الأول</th>
                                                <td class="fw-medium">{{ $user->first_name }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted ps-4">الاسم الأخير</th>
                                                <td class="fw-medium">{{ $user->last_name }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted ps-4">البريد الإلكتروني</th>
                                                <td class="fw-medium">{{ $user->email }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted ps-4">رقم الهاتف</th>
                                                <td>{{ $user->phone ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted ps-4">المسمى الوظيفي</th>
                                                <td>{{ $user->job_title ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted ps-4">القسم</th>
                                                <td>{{ $user->department ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted ps-4">الصلاحية</th>
                                                <td>
                                                    @forelse($user->roles as $role)
                                                        <span class="badge bg-info-subtle text-info">{{ $role->name }}</span>
                                                    @empty
                                                        <span class="text-muted">-</span>
                                                    @endforelse
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted ps-4">الحالة</th>
                                                <td>
                                                    @if($user->trashed())
                                                        <span class="badge bg-secondary-subtle text-secondary">محذوف</span>
                                                    @elseif($user->is_active)
                                                        <span class="badge bg-success-subtle text-success">نشط</span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger">غير نشط</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted ps-4">تاريخ الإنشاء</th>
                                                <td>{{ $user->created_at->format('Y/m/d h:i A') }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted ps-4">آخر تحديث</th>
                                                <td>{{ $user->updated_at->format('Y/m/d h:i A') }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted ps-4">آخر دخول</th>
                                                <td>
                                                    @if($user->last_login_at)
                                                        {{ $user->last_login_at->format('Y/m/d h:i A') }}
                                                        <br><small class="text-muted">{{ $user->last_login_at->diffForHumans() }}</small>
                                                    @else
                                                        <span class="text-muted">لم يسجل دخول بعد</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @if($user->last_login_ip)
                                            <tr>
                                                <th class="text-muted ps-4">آخر IP</th>
                                                <td><code class="text-primary">{{ $user->last_login_ip }}</code></td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        @if($user->bio)
                        <div class="border-0 shadow card">
                            <div class="card-header">
                                <h5 class="mb-0 card-title">
                                    <i class="ti ti-file-text me-2"></i>نبذة
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $user->bio }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    @if(!$user->trashed())
    <div class="modal fade" id="editUserShowModal" tabindex="-1" aria-labelledby="editUserShowModalLabel">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserShowModalLabel">
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
                                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="editShowUserActive" {{ $user->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="editShowUserActive">نشط</label>
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

    {{-- Change Password Modal --}}
    <div class="modal fade" id="changePasswordShowModal" tabindex="-1" aria-labelledby="changePasswordShowModalLabel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordShowModalLabel">
                        <i class="ti ti-key me-2"></i>تغيير كلمة المرور
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="changePasswordShowForm" onsubmit="changePasswordShow(event)">
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

    {{-- Delete Modal --}}
    @if($user->id != auth()->id())
    <div class="modal fade" id="deleteUserShowModal" tabindex="-1" aria-labelledby="deleteUserShowModalLabel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="text-white modal-header bg-danger">
                    <h5 class="modal-title" id="deleteUserShowModalLabel">
                        <i class="ti ti-alert-triangle me-2"></i>تأكيد الحذف
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="py-4 text-center modal-body">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="mb-3 rounded-circle" width="80" height="80">
                    @else
                        <div class="mx-auto mb-3 avatar-lg">
                            <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-2">
                                {{ $user->initials }}
                            </span>
                        </div>
                    @endif
                    <h5 class="mb-2">{{ $user->name }}</h5>
                    <p class="mb-0 text-muted">{{ $user->email }}</p>
                    <hr>
                    <p class="mb-1">هل أنت متأكد من حذف هذا المستخدم؟</p>
                    <small class="text-muted">يمكن استعادته لاحقاً من سلة المحذوفات</small>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>إلغاء
                    </button>
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="ti ti-trash me-1"></i>نعم، احذف
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endif

    @include('admin.main.scripts')

    <script>
    function toggleUserStatus(userId) {
        fetch(`{{ url('admin/users') }}/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'حدث خطأ');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ');
        });
    }

    function changePasswordShow(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);

        fetch(`{{ route('admin.users.change-password', $user->id) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                password: formData.get('password'),
                password_confirmation: formData.get('password_confirmation')
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                bootstrap.Modal.getInstance(document.getElementById('changePasswordShowModal')).hide();
                form.reset();
            } else {
                alert(data.message || 'حدث خطأ');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء تغيير كلمة المرور');
        });
    }
    </script>
</body>
</html>
