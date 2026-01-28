<!DOCTYPE html>
@include('admin.main.html')

<head>
    <title>إدارة الصلاحيات - أرشيف القاهرة الجديدة</title>
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
                                <i class="ti ti-shield-lock me-2"></i>إدارة الصلاحيات
                            </h4>
                            <div class="page-title-right">
                                <ol class="m-0 breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                                    <li class="breadcrumb-item active">الصلاحيات</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stats Cards --}}
                <div class="mb-3 row">
                    <div class="col-xl-6 col-md-6">
                        <div class="border-0 shadow card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-3">
                                            <i class="ti ti-shield"></i>
                                        </span>
                                    </div>
                                    <div class="ms-3">
                                        <p class="mb-1 text-muted">إجمالي الصلاحيات</p>
                                        <h4 class="mb-0 fw-bold">{{ $roles->total() }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-md-6">
                        <div class="border-0 shadow card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-info-subtle text-info rounded-circle fs-3">
                                            <i class="ti ti-users"></i>
                                        </span>
                                    </div>
                                    <div class="ms-3">
                                        <p class="mb-1 text-muted">المستخدمون المعينون</p>
                                        <h4 class="mb-0 fw-bold">{{ $roles->sum('users_count') }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Search & Add --}}
                <div class="mb-3 card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 card-title"><i class="ti ti-filter me-1"></i>فلتر البحث</h5>
                        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>إضافة صلاحية
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.roles.index') }}" method="GET">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-9">
                                    <label class="form-label"><i class="ti ti-search me-1"></i>بحث</label>
                                    <input type="text" name="search" class="form-control" placeholder="ابحث باسم الصلاحية..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <div class="gap-2 d-flex">
                                        <button type="submit" class="btn btn-primary flex-grow-1">
                                            <i class="ti ti-search me-1"></i>بحث
                                        </button>
                                        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                                            <i class="ti ti-refresh"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Roles Table --}}
                <div class="card">
                    {{-- Bulk Actions Bar --}}
                    <div id="bulkActionsBar" class="m-3 mb-0 alert alert-primary d-none">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="ti ti-checkbox me-2"></i>
                                <span id="selectedCount">0</span> صلاحية محددة
                            </div>
                            <div class="gap-2 d-flex">
                                <button type="button" class="btn btn-sm btn-light text-danger" onclick="bulkDelete()">
                                    <i class="ti ti-trash me-1"></i>حذف المحدد
                                </button>
                                <button type="button" class="btn btn-sm btn-light text-primary" onclick="clearSelection()">
                                    <i class="ti ti-x me-1"></i>إلغاء
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 card-title"><i class="ti ti-table me-1"></i>قائمة الصلاحيات</h5>
                        <span class="badge bg-primary">{{ $roles->total() }} صلاحية</span>
                    </div>
                    <div class="p-0 card-body">
                        @if($roles->count() > 0)
                        <div class="table-responsive">
                            <table class="table mb-0 table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40px;">
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                        </th>
                                        <th>اسم الصلاحية</th>
                                        <th class="text-center">المستخدمون</th>
                                        <th class="text-center">الأذونات</th>
                                        <th>تاريخ الإنشاء</th>
                                        <th class="text-center" style="width: 150px;">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $role)
                                    <tr>
                                        <td>
                                            @if(!in_array($role->name, ['super-admin', 'admin']))
                                            <input type="checkbox" class="form-check-input role-checkbox" value="{{ $role->id }}">
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <span class="avatar-title bg-{{ in_array($role->name, ['super-admin', 'admin']) ? 'danger' : 'primary' }}-subtle text-{{ in_array($role->name, ['super-admin', 'admin']) ? 'danger' : 'primary' }} rounded-circle">
                                                        <i class="ti ti-shield{{ in_array($role->name, ['super-admin', 'admin']) ? '-lock' : '' }}"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="fw-semibold">{{ $role->name }}</span>
                                                    @if(in_array($role->name, ['super-admin', 'admin']))
                                                    <br><small class="text-danger">صلاحية نظام</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary-subtle text-primary fs-5">{{ $role->users_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary-subtle text-primary fs-5">{{ $role->permissions->count() }}</span>
                                        </td>
                                        <td><span class="text-dark">{{ $role->created_at->format('Y/m/d') }}</span></td>
                                        <td class="text-center">
                                            <div class="gap-1 d-flex justify-content-center">
                                                <a href="{{ route('admin.roles.show', $role->id) }}" class="px-2 btn btn-sm bg-primary-subtle text-primary" title="عرض">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.roles.edit', $role->id) }}" class="px-2 btn btn-sm bg-warning-subtle text-warning" title="تعديل">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                                @if(!in_array($role->name, ['super-admin', 'admin']) && $role->users_count == 0)
                                                <button type="button" class="px-2 btn btn-sm bg-danger-subtle text-danger" title="حذف" data-bs-toggle="modal" data-bs-target="#deleteRoleModal_{{ $role->id }}">
                                                    <i class="ti ti-trash"></i>
                                                </button>
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
                                    <i class="ti ti-shield-off"></i>
                                </span>
                            </div>
                            <h5 class="text-muted">لا يوجد صلاحيات</h5>
                            <a href="{{ route('admin.roles.create') }}" class="mt-2 btn btn-primary">
                                <i class="ti ti-plus me-1"></i>إضافة صلاحية
                            </a>
                        </div>
                        @endif
                    </div>
                    @if($roles->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">عرض {{ $roles->firstItem() }} إلى {{ $roles->lastItem() }} من {{ $roles->total() }}</small>
                            {{ $roles->links() }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Role Modals --}}
    @foreach($roles as $role)
    @if(!in_array($role->name, ['super-admin', 'admin']) && $role->users_count == 0)
    <div class="modal fade" id="deleteRoleModal_{{ $role->id }}" tabindex="-1" aria-labelledby="deleteRoleModalLabel_{{ $role->id }}">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="text-white modal-header bg-danger">
                    <h5 class="modal-title" id="deleteRoleModalLabel_{{ $role->id }}">
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
                    <p class="text-muted">{{ $role->permissions->count() }} أذن</p>
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
    @endforeach

    @include('admin.main.scripts')

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const bulkBar = document.getElementById('bulkActionsBar');
        const countSpan = document.getElementById('selectedCount');

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.role-checkbox').forEach(cb => cb.checked = this.checked);
                updateBulkBar();
            });
        }

        document.querySelectorAll('.role-checkbox').forEach(cb => {
            cb.addEventListener('change', updateBulkBar);
        });

        function updateBulkBar() {
            const count = document.querySelectorAll('.role-checkbox:checked').length;
            const total = document.querySelectorAll('.role-checkbox').length;
            bulkBar.classList.toggle('d-none', count === 0);
            countSpan.textContent = count;
            if (selectAll) {
                selectAll.checked = count === total && total > 0;
                selectAll.indeterminate = count > 0 && count < total;
            }
        }
    });

    function bulkDelete() {
        const ids = Array.from(document.querySelectorAll('.role-checkbox:checked')).map(cb => cb.value);
        if (ids.length === 0) return;
        if (!confirm(`هل أنت متأكد من حذف ${ids.length} صلاحية؟`)) return;

        fetch('{{ route("admin.roles.bulk-delete") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ ids: ids })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) location.reload();
            else alert(data.message || 'حدث خطأ');
        })
        .catch(() => alert('حدث خطأ'));
    }

    function clearSelection() {
        document.querySelectorAll('.role-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('selectAll').checked = false;
        document.getElementById('selectAll').indeterminate = false;
        document.getElementById('bulkActionsBar').classList.add('d-none');
        document.getElementById('selectedCount').textContent = '0';
    }
    </script>
</body>
</html>
