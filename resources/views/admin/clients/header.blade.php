<div class="p-2 mt-3 mb-4 bg-white rounded border-0 shadow card">
    <div class="row align-items-center">
        <div class="col-auto">
            <div class="page-icon">
                <div class="avatar avatar-lg bg-primary-subtle rounded-3">
                    <i class="ti ti-users fs-2 text-primary"></i>
                </div>
            </div>
        </div>
        <div class="col">
            <h4 class="mb-1">إدارة العملاء</h4>
            <div class="text-primary">
                <span class="badge bg-primary-subtle text-primary me-2">{{ \App\Models\Client::count() }} عميل</span>
                عرض وإدارة جميع العملاء والملفات المرتبطة بهم
            </div>
        </div>
        <div class="col-auto">
            <div class="gap-2 btn-list">
                @can('clients.delete')
                <a href="{{ route('admin.clients.trash') }}" class="btn btn-ghost-danger">
                    <i class="ti ti-trash me-1"></i>
                    <span class="d-none d-sm-inline">سلة المحذوفات</span>
                    @php $trashedCount = \App\Models\Client::onlyTrashed()->count(); @endphp
                    @if($trashedCount > 0)
                        <span class="badge bg-danger ms-1">{{ $trashedCount }}</span>
                    @endif
                </a>
                @endcan
                @can('clients.export')
                <a href="{{ route('admin.clients.export') }}" class="btn btn-ghost-success">
                    <i class="ti ti-file-spreadsheet me-1"></i>
                    <span class="d-none d-sm-inline">تصدير Excel</span>
                </a>
                @endcan
                @can('clients.create')
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClientModal">
                    <i class="ti ti-plus me-1"></i>إضافة عميل
                </button>
                @endcan
            </div>
        </div>
    </div>
</div>
