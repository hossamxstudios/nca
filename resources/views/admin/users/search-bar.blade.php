<div class="mb-3 card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 card-title">
            <i class="ti ti-filter me-1"></i>فلتر البحث
        </h5>
        @can('users.create')
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="ti ti-plus me-1"></i>إضافة مستخدم جديد
        </button>
        @endcan
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-medium">
                        <i class="ti ti-search me-1"></i>بحث
                    </label>
                    <input type="text" name="search" class="form-control" placeholder="الاسم، البريد، الهاتف..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-medium">
                        <i class="ti ti-shield me-1"></i>الصلاحية
                    </label>
                    <select name="role" class="form-select">
                        <option value="">الكل</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-medium">
                        <i class="ti ti-toggle-left me-1"></i>الحالة
                    </label>
                    <select name="status" class="form-select">
                        <option value="">الكل</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-medium">
                        <i class="ti ti-trash me-1"></i>المحذوفين
                    </label>
                    <select name="trashed" class="form-select">
                        <option value="">النشطين فقط</option>
                        <option value="only" {{ request('trashed') == 'only' ? 'selected' : '' }}>المحذوفين فقط</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="gap-2 d-flex">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="ti ti-search me-1"></i>بحث
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary" title="إعادة تعيين">
                            <i class="ti ti-refresh"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
