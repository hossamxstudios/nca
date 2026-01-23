<!DOCTYPE html>
@include('admin.main.html')

<head>
    <title>{{ $role->name }} - أرشيف القاهرة الجديدة</title>
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
                                <i class="ti ti-shield me-2"></i>تفاصيل الصلاحية
                            </h4>
                            <div class="page-title-right">
                                <ol class="m-0 breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">الصلاحيات</a></li>
                                    <li class="breadcrumb-item active">{{ $role->name }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Role Info Card --}}
                    <div class="col-lg-4">
                        <div class="border-0 shadow card">
                            <div class="py-4 text-center card-body">
                                <div class="mx-auto mb-3 avatar-xl">
                                    <span class="avatar-title bg-{{ in_array($role->name, ['super-admin', 'admin']) ? 'danger' : 'primary' }}-subtle text-{{ in_array($role->name, ['super-admin', 'admin']) ? 'danger' : 'primary' }} rounded-circle fs-1">
                                        <i class="ti ti-shield{{ in_array($role->name, ['super-admin', 'admin']) ? '-lock' : '' }}"></i>
                                    </span>
                                </div>
                                <h4 class="mb-1 fw-semibold">{{ $role->name }}</h4>
                                @if(in_array($role->name, ['super-admin', 'admin']))
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2">صلاحية نظام</span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary px-3 py-2">صلاحية مخصصة</span>
                                @endif
                            </div>
                            <div class="pt-0 card-body">
                                <hr class="my-3">
                                <div class="mb-3 d-flex align-items-center">
                                    <div class="avatar-xs me-3">
                                        <span class="avatar-title bg-info-subtle text-info rounded-circle">
                                            <i class="ti ti-users"></i>
                                        </span>
                                    </div>
                                    <span>{{ $role->users->count() }} مستخدم</span>
                                </div>
                                <div class="mb-3 d-flex align-items-center">
                                    <div class="avatar-xs me-3">
                                        <span class="avatar-title bg-success-subtle text-success rounded-circle">
                                            <i class="ti ti-key"></i>
                                        </span>
                                    </div>
                                    <span>{{ $role->permissions->count() }} أذن</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs me-3">
                                        <span class="avatar-title bg-warning-subtle text-warning rounded-circle">
                                            <i class="ti ti-calendar"></i>
                                        </span>
                                    </div>
                                    <span>أُنشئت {{ $role->created_at->format('Y/m/d') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="border-0 shadow card">
                            <div class="card-header">
                                <h5 class="mb-0 card-title"><i class="ti ti-settings me-2"></i>الإجراءات</h5>
                            </div>
                            <div class="card-body">
                                <div class="gap-2 d-grid">
                                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-outline-warning">
                                        <i class="ti ti-edit me-1"></i>تعديل الصلاحية
                                    </a>
                                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                                        <i class="ti ti-arrow-right me-1"></i>العودة للقائمة
                                    </a>
                                    @if(!in_array($role->name, ['super-admin', 'admin']) && $role->users->count() == 0)
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteRoleShowModal">
                                        <i class="ti ti-trash me-1"></i>حذف الصلاحية
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Permissions Card --}}
                    <div class="col-lg-8">
                        <div class="border-0 shadow card">
                            <div class="card-header">
                                <h5 class="mb-0 card-title"><i class="ti ti-key me-2"></i>الأذونات المعينة</h5>
                            </div>
                            <div class="card-body">
                                @if($role->permissions->count() > 0)
                                <div class="accordion" id="permissionsAccordion">
                                    @foreach($groupedPermissions as $module => $modulePermissions)
                                    @php
                                        $rolePermNames = $role->permissions->pluck('name');
                                        $hasPerms = $modulePermissions->pluck('name')->intersect($rolePermNames)->count() > 0;
                                    @endphp
                                    @if($hasPerms)
                                    @php
                                        $moduleNameAr = $moduleTranslations[$module] ?? $module;
                                    @endphp
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#show_{{ Str::slug($module) }}">
                                                <i class="ti ti-folder me-2"></i>{{ $moduleNameAr }}
                                                <span class="badge bg-success ms-2">
                                                    {{ $modulePermissions->pluck('name')->intersect($rolePermNames)->count() }}
                                                </span>
                                            </button>
                                        </h2>
                                        <div id="show_{{ Str::slug($module) }}" class="accordion-collapse collapse" data-bs-parent="#permissionsAccordion">
                                            <div class="accordion-body">
                                                <div class="row g-2">
                                                    @foreach($modulePermissions as $permission)
                                                    @if($role->hasPermissionTo($permission->name))
                                                    @php
                                                        // Dynamic translation: extract action from permission name
                                                        // Supports: clients.view -> view, clients.bulk-delete -> bulk-delete
                                                        $permName = $permission->name;
                                                        $action = '';
                                                        if (str_contains($permName, '.')) {
                                                            $parts = explode('.', $permName);
                                                            $action = $parts[1] ?? '';
                                                        }
                                                        $permNameAr = $actionTranslations[$action] ?? $action ?: $permName;
                                                    @endphp
                                                    <div class="col-md-4 col-sm-6">
                                                        <span class="badge bg-success-subtle text-success">
                                                            <i class="ti ti-check me-1"></i>{{ $permNameAr }}
                                                        </span>
                                                    </div>
                                                    @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                                @else
                                <div class="py-4 text-center">
                                    <div class="mx-auto mb-3 avatar-lg">
                                        <span class="avatar-title bg-light text-muted rounded-circle fs-1">
                                            <i class="ti ti-key-off"></i>
                                        </span>
                                    </div>
                                    <h5 class="text-muted">لا توجد أذونات</h5>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Users with this role --}}
                        @if($role->users->count() > 0)
                        <div class="border-0 shadow card">
                            <div class="card-header">
                                <h5 class="mb-0 card-title"><i class="ti ti-users me-2"></i>المستخدمون بهذه الصلاحية</h5>
                            </div>
                            <div class="p-0 card-body">
                                <div class="table-responsive">
                                    <table class="table mb-0 table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>المستخدم</th>
                                                <th>البريد</th>
                                                <th class="text-center">الحالة</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($role->users->take(10) as $user)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <span class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                                {{ $user->initials ?? substr($user->name ?? $user->email, 0, 2) }}
                                                            </span>
                                                        </div>
                                                        <span class="fw-medium">{{ $user->name ?? $user->email }}</span>
                                                    </div>
                                                </td>
                                                <td>{{ $user->email }}</td>
                                                <td class="text-center">
                                                    @if($user->is_active ?? true)
                                                        <span class="badge bg-success-subtle text-success">نشط</span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger">غير نشط</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($role->users->count() > 10)
                                <div class="p-3 text-center border-top">
                                    <small class="text-muted">و {{ $role->users->count() - 10 }} مستخدم آخر...</small>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    @if(!in_array($role->name, ['super-admin', 'admin']) && $role->users->count() == 0)
    <div class="modal fade" id="deleteRoleShowModal" tabindex="-1" aria-labelledby="deleteRoleShowModalLabel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="text-white modal-header bg-danger">
                    <h5 class="modal-title" id="deleteRoleShowModalLabel">
                        <i class="ti ti-alert-triangle me-2"></i>تأكيد الحذف
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="py-4 text-center modal-body">
                    <div class="mx-auto mb-3 avatar-lg">
                        <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-2">
                            <i class="ti ti-shield-x"></i>
                        </span>
                    </div>
                    <h5 class="mb-2">{{ $role->name }}</h5>
                    <hr>
                    <p class="mb-1">هل أنت متأكد من حذف هذه الصلاحية؟</p>
                    <small class="text-danger">هذا الإجراء لا يمكن التراجع عنه</small>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>إلغاء
                    </button>
                    <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline">
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

    @include('admin.main.scripts')
</body>
</html>
