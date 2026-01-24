<div class="mb-4 border-0 shadow-sm card">
    <div class="card-header bg-white">
        <h5 class="mb-0 card-title">
            <i class="ti ti-filter me-2"></i>فلترة السجلات
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" id="filterForm">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">المستخدم</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">الكل</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">نوع الإجراء</label>
                    <select name="action_type" class="form-select form-select-sm">
                        <option value="">الكل</option>
                        @foreach($actionTypes as $key => $label)
                            <option value="{{ $key }}" {{ request('action_type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">القسم</label>
                    <select name="action_group" class="form-select form-select-sm">
                        <option value="">الكل</option>
                        @foreach($actionGroups as $key => $label)
                            <option value="{{ $key }}" {{ request('action_group') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">من تاريخ</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">إلى تاريخ</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">بحث</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="بحث..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="gap-2 mt-3 d-flex">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="ti ti-search me-1"></i>بحث
                </button>
                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary btn-sm">
                    <i class="ti ti-refresh me-1"></i>إعادة تعيين
                </a>
            </div>
        </form>
    </div>
</div>
