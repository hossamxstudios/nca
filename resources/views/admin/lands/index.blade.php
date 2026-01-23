<!DOCTYPE html>
@include('admin.main.html')

<head>
    <title>إدارة الأراضي - أرشيف القاهرة الجديدة</title>
    @include('admin.main.meta')
    <style>
        .hierarchy-row { cursor: pointer; }
        .hierarchy-row:hover { background-color: rgba(var(--bs-primary-rgb), 0.05); }
        .hierarchy-children { display: none; }
        .hierarchy-children.show { display: table-row-group; }
        .level-1 { background-color: rgba(var(--bs-primary-rgb), 0.03); }
        .level-2 { background-color: rgba(var(--bs-info-rgb), 0.03); }
        .level-3 { background-color: rgba(var(--bs-success-rgb), 0.03); }
        .level-4 { background-color: rgba(var(--bs-warning-rgb), 0.03); }
        .indent-1 { padding-right: 2rem !important; }
        .indent-2 { padding-right: 3rem !important; }
        .indent-3 { padding-right: 4rem !important; }
        .indent-4 { padding-right: 5rem !important; }
        .toggle-icon { transition: transform 0.2s; }
        .toggle-icon.rotated { transform: rotate(-90deg); }
    </style>
</head>

<body>
    <div class="wrapper">
        @include('admin.main.topbar')
        @include('admin.main.sidebar')
        <div class="content-page">
            <div class="container-fluid">
                {{-- Header --}}
                <div class="py-3 d-flex justify-content-between align-items-center page-title-box">
                    <div>
                        <h4 class="mb-1 page-title">إدارة الأراضي</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="p-0 mb-0 breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                                <li class="breadcrumb-item active">الأراضي</li>
                            </ol>
                        </nav>
                    </div>
                    @can('lands.create')
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLandModal">
                        <i class="ti ti-plus me-1"></i>إضافة أرض
                    </button>
                    @endcan
                </div>

                {{-- Stats Cards --}}
                <div class="mb-4 row g-3">
                    <div class="col-md-4">
                        <div class="border-0 shadow-sm card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle avatar-md bg-primary-subtle d-flex align-items-center justify-content-center">
                                            <i class="ti ti-map-2 fs-3 text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h3 class="mb-0">{{ number_format($totalLands) }}</h3>
                                        <p class="mb-0 text-muted">إجمالي الأراضي</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border-0 shadow-sm card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle avatar-md bg-success-subtle d-flex align-items-center justify-content-center">
                                            <i class="ti ti-files fs-3 text-success"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h3 class="mb-0">{{ number_format($landsWithFiles) }}</h3>
                                        <p class="mb-0 text-muted">أراضي بها ملفات</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border-0 shadow-sm card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle avatar-md bg-danger-subtle d-flex align-items-center justify-content-center">
                                            <i class="ti ti-trash fs-3 text-danger"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h3 class="mb-0">{{ number_format($trashedLands) }}</h3>
                                        <p class="mb-0 text-muted">أراضي محذوفة</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Search & Filters --}}
                <div class="mb-4 border-0 shadow-sm card">
                    <div class="card-body">
                        <form action="{{ route('admin.lands.index') }}" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-search"></i></span>
                                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="بحث برقم القطعة أو العنوان...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="client_id" class="form-select">
                                    <option value="">كل العملاء</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="governorate_id" class="form-select">
                                    <option value="">كل المحافظات</option>
                                    @foreach($governorates as $gov)
                                        <option value="{{ $gov->id }}" {{ request('governorate_id') == $gov->id ? 'selected' : '' }}>
                                            {{ $gov->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="gap-2 d-flex">
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i class="ti ti-filter"></i>
                                    </button>
                                    <a href="{{ route('admin.lands.index') }}" class="btn btn-outline-secondary">
                                        <i class="ti ti-x"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Bulk Actions Bar --}}
                <div class="mb-3 alert alert-primary d-none" id="bulkActionsBar">
                    <div class="d-flex align-items-center justify-content-between">
                        <span>تم تحديد <strong id="selectedCount">0</strong> عنصر</span>
                        <div class="gap-2 d-flex">
                            @can('lands.delete')
                            <button type="button" class="btn btn-sm btn-danger" onclick="bulkDelete()">
                                <i class="ti ti-trash me-1"></i>حذف المحدد
                            </button>
                            @endcan
                            @if(request('trashed') === 'only')
                            @can('lands.restore')
                            <button type="button" class="btn btn-sm btn-success" onclick="bulkRestore()">
                                <i class="ti ti-refresh me-1"></i>استعادة المحدد
                            </button>
                            @endcan
                            @can('lands.force-delete')
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="bulkForceDelete()">
                                <i class="ti ti-trash-x me-1"></i>حذف نهائي
                            </button>
                            @endcan
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Lands Table with Hierarchy --}}
                <div class="border-0 shadow-sm card">
                    <div class="p-0 card-body">
                        @if($lands->count() > 0)
                        <div class="table-responsive">
                            <table class="table mb-0 align-middle table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" class="form-check-input" id="selectAll" onchange="toggleSelectAll()">
                                        </th>
                                        <th>العميل</th>
                                        <th>الموقع الجغرافي</th>
                                        <th>رقم القطعة</th>
                                        <th>رقم الوحدة</th>
                                        <th>موقع التخزين</th>
                                        <th>الملفات</th>
                                        <th width="150">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lands as $land)
                                    <tr class="{{ $land->trashed() ? 'table-danger' : '' }}">
                                        <td>
                                            <input type="checkbox" class="form-check-input land-checkbox" value="{{ $land->id }}" onchange="updateBulkActions()">
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.clients.show', $land->client_id) }}" class="fw-medium text-decoration-none">
                                                {{ $land->client->name ?? '-' }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                @if($land->governorate)
                                                <small class="text-muted">
                                                    <i class="ti ti-map-pin me-1"></i>
                                                    {{ $land->governorate->name }}
                                                    @if($land->city) / {{ $land->city->name }} @endif
                                                </small>
                                                @endif
                                                @if($land->district || $land->zone || $land->area)
                                                <small class="text-muted">
                                                    {{ collect([$land->district?->name, $land->zone?->name, $land->area?->name])->filter()->join(' / ') }}
                                                </small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary">{{ $land->land_no }}</span>
                                        </td>
                                        <td>
                                            @if($land->unit_no)
                                                <span class="badge bg-info-subtle text-info">{{ $land->unit_no }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($land->room || $land->lane || $land->stand || $land->rack)
                                            <small class="text-muted">
                                                <i class="ti ti-building-warehouse me-1"></i>
                                                {{ collect([$land->room?->name, $land->lane?->name, $land->stand?->name, $land->rack?->name])->filter()->join(' / ') }}
                                            </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $land->files_count ?? $land->files->count() }}</span>
                                        </td>
                                        <td>
                                            <div class="gap-1 d-flex">
                                                @can('lands.view')
                                                <a href="{{ route('admin.lands.show', $land) }}" class="btn btn-sm bg-info-subtle text-info" title="عرض">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                @endcan

                                                @if($land->trashed())
                                                    @can('lands.restore')
                                                    <form action="{{ route('admin.lands.restore', $land->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm bg-success-subtle text-success" title="استعادة">
                                                            <i class="ti ti-refresh"></i>
                                                        </button>
                                                    </form>
                                                    @endcan
                                                    @can('lands.force-delete')
                                                    <form action="{{ route('admin.lands.force-delete', $land->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف النهائي؟')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm bg-danger-subtle text-danger" title="حذف نهائي">
                                                            <i class="ti ti-trash-x"></i>
                                                        </button>
                                                    </form>
                                                    @endcan
                                                @else
                                                    @can('lands.edit')
                                                    <button type="button" class="btn btn-sm bg-warning-subtle text-warning" title="تعديل"
                                                            data-bs-toggle="modal" data-bs-target="#editLandModal_{{ $land->id }}">
                                                        <i class="ti ti-edit"></i>
                                                    </button>
                                                    @endcan
                                                    @can('lands.delete')
                                                    <form action="{{ route('admin.lands.destroy', $land) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm bg-danger-subtle text-danger" title="حذف">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </form>
                                                    @endcan
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="p-3 border-top">
                            {{ $lands->links() }}
                        </div>
                        @else
                        <div class="py-5 text-center">
                            <i class="mb-3 ti ti-map-off fs-1 text-muted"></i>
                            <h5 class="text-muted">لا توجد أراضي</h5>
                            <p class="text-muted">لم يتم العثور على أي أراضي مسجلة</p>
                            @can('lands.create')
                            <button type="button" class="mt-2 btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLandModal">
                                <i class="ti ti-plus me-1"></i>إضافة أرض
                            </button>
                            @endcan
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Hierarchy View Card --}}
                <div class="mt-4 border-0 shadow-sm card">
                    <div class="card-header">
                        <h5 class="mb-0 card-title"><i class="ti ti-hierarchy-2 me-2"></i>عرض التسلسل الهرمي</h5>
                    </div>
                    <div class="p-0 card-body">
                        <div class="table-responsive">
                            <table class="table mb-0 table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>المحافظة / المدينة / الحي</th>
                                        <th>عدد الأراضي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($governorates as $gov)
                                    @if($gov->lands_count > 0)
                                    <tr class="hierarchy-row level-1" data-toggle="gov-{{ $gov->id }}">
                                        <td>
                                            <i class="ti ti-chevron-down toggle-icon me-2"></i>
                                            <i class="ti ti-building me-1 text-primary"></i>
                                            <strong>{{ $gov->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $gov->lands_count }}</span>
                                        </td>
                                    </tr>
                                    <tbody class="hierarchy-children" id="gov-{{ $gov->id }}">
                                        @foreach($gov->cities as $city)
                                        @php $cityLandsCount = \App\Models\Land::where('city_id', $city->id)->count(); @endphp
                                        @if($cityLandsCount > 0)
                                        <tr class="hierarchy-row level-2" data-toggle="city-{{ $city->id }}">
                                            <td class="indent-1">
                                                <i class="ti ti-chevron-down toggle-icon me-2"></i>
                                                <i class="ti ti-map-pin me-1 text-info"></i>
                                                {{ $city->name }}
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $cityLandsCount }}</span>
                                            </td>
                                        </tr>
                                        <tbody class="hierarchy-children" id="city-{{ $city->id }}">
                                            @foreach($city->districts as $district)
                                            @php $districtLandsCount = \App\Models\Land::where('district_id', $district->id)->count(); @endphp
                                            @if($districtLandsCount > 0)
                                            <tr class="level-3">
                                                <td class="indent-2">
                                                    <i class="ti ti-point me-2 text-muted"></i>
                                                    <i class="ti ti-map me-1 text-success"></i>
                                                    {{ $district->name }}
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">{{ $districtLandsCount }}</span>
                                                </td>
                                            </tr>
                                            @endif
                                            @endforeach
                                        </tbody>
                                        @endif
                                        @endforeach
                                    </tbody>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Land Modal --}}
    @can('lands.create')
    <div class="modal fade" id="addLandModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.lands.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-plus me-2"></i>إضافة أرض جديدة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">العميل <span class="text-danger">*</span></label>
                                <select name="client_id" class="form-select" required>
                                    <option value="">اختر العميل</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">رقم القطعة <span class="text-danger">*</span></label>
                                <input type="text" name="land_no" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">رقم الوحدة</label>
                                <input type="text" name="unit_no" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">المحافظة</label>
                                <select name="governorate_id" class="form-select" id="add_governorate" onchange="loadCities(this.value, 'add_city')">
                                    <option value="">اختر المحافظة</option>
                                    @foreach($governorates as $gov)
                                        <option value="{{ $gov->id }}">{{ $gov->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">المدينة</label>
                                <select name="city_id" class="form-select" id="add_city" onchange="loadDistricts(this.value, 'add_district')">
                                    <option value="">اختر المدينة</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الحي</label>
                                <select name="district_id" class="form-select" id="add_district" onchange="loadZones(this.value, 'add_zone')">
                                    <option value="">اختر الحي</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">المنطقة</label>
                                <select name="zone_id" class="form-select" id="add_zone" onchange="loadAreas(this.value, 'add_area')">
                                    <option value="">اختر المنطقة</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">القسم</label>
                                <select name="area_id" class="form-select" id="add_area">
                                    <option value="">اختر القسم</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">العنوان التفصيلي</label>
                                <textarea name="address" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-check me-1"></i>حفظ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    {{-- Edit Land Modals --}}
    @can('lands.edit')
    @foreach($lands as $land)
    @if(!$land->trashed())
    <div class="modal fade" id="editLandModal_{{ $land->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.lands.update', $land) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-edit me-2"></i>تعديل بيانات الأرض</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">العميل <span class="text-danger">*</span></label>
                                <select name="client_id" class="form-select" required>
                                    <option value="">اختر العميل</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ $land->client_id == $client->id ? 'selected' : '' }}>
                                            {{ $client->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">رقم القطعة <span class="text-danger">*</span></label>
                                <input type="text" name="land_no" class="form-control" value="{{ $land->land_no }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">رقم الوحدة</label>
                                <input type="text" name="unit_no" class="form-control" value="{{ $land->unit_no }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">المحافظة</label>
                                <select name="governorate_id" class="form-select" id="edit_governorate_{{ $land->id }}"
                                        onchange="loadCities(this.value, 'edit_city_{{ $land->id }}')">
                                    <option value="">اختر المحافظة</option>
                                    @foreach($governorates as $gov)
                                        <option value="{{ $gov->id }}" {{ $land->governorate_id == $gov->id ? 'selected' : '' }}>
                                            {{ $gov->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">المدينة</label>
                                <select name="city_id" class="form-select" id="edit_city_{{ $land->id }}"
                                        onchange="loadDistricts(this.value, 'edit_district_{{ $land->id }}')"
                                        data-selected="{{ $land->city_id }}">
                                    <option value="">اختر المدينة</option>
                                    @if($land->city)
                                        <option value="{{ $land->city_id }}" selected>{{ $land->city->name }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الحي</label>
                                <select name="district_id" class="form-select" id="edit_district_{{ $land->id }}"
                                        onchange="loadZones(this.value, 'edit_zone_{{ $land->id }}')"
                                        data-selected="{{ $land->district_id }}">
                                    <option value="">اختر الحي</option>
                                    @if($land->district)
                                        <option value="{{ $land->district_id }}" selected>{{ $land->district->name }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">المنطقة</label>
                                <select name="zone_id" class="form-select" id="edit_zone_{{ $land->id }}"
                                        onchange="loadAreas(this.value, 'edit_area_{{ $land->id }}')"
                                        data-selected="{{ $land->zone_id }}">
                                    <option value="">اختر المنطقة</option>
                                    @if($land->zone)
                                        <option value="{{ $land->zone_id }}" selected>{{ $land->zone->name }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">القسم</label>
                                <select name="area_id" class="form-select" id="edit_area_{{ $land->id }}"
                                        data-selected="{{ $land->area_id }}">
                                    <option value="">اختر القسم</option>
                                    @if($land->area)
                                        <option value="{{ $land->area_id }}" selected>{{ $land->area->name }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">العنوان التفصيلي</label>
                                <textarea name="address" class="form-control" rows="2">{{ $land->address }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="2">{{ $land->notes }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
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
    @endcan

    @include('admin.main.scripts')

    <script>
        // Select All
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            document.querySelectorAll('.land-checkbox').forEach(cb => cb.checked = selectAll.checked);
            updateBulkActions();
        }

        // Update Bulk Actions Bar
        function updateBulkActions() {
            const checked = document.querySelectorAll('.land-checkbox:checked');
            const bar = document.getElementById('bulkActionsBar');
            document.getElementById('selectedCount').textContent = checked.length;
            bar.classList.toggle('d-none', checked.length === 0);
        }

        // Get Selected IDs
        function getSelectedIds() {
            return Array.from(document.querySelectorAll('.land-checkbox:checked')).map(cb => cb.value);
        }

        // Bulk Delete
        function bulkDelete() {
            if (!confirm('هل أنت متأكد من حذف العناصر المحددة؟')) return;
            fetch('{{ route("admin.lands.bulk-delete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ids: getSelectedIds() })
            }).then(r => r.json()).then(d => {
                if (d.success) location.reload();
                else alert(d.message);
            });
        }

        // Bulk Restore
        function bulkRestore() {
            if (!confirm('هل أنت متأكد من استعادة العناصر المحددة؟')) return;
            fetch('{{ route("admin.lands.bulk-restore") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ids: getSelectedIds() })
            }).then(r => r.json()).then(d => {
                if (d.success) location.reload();
                else alert(d.message);
            });
        }

        // Bulk Force Delete
        function bulkForceDelete() {
            if (!confirm('هل أنت متأكد من الحذف النهائي للعناصر المحددة؟ لا يمكن التراجع عن هذا الإجراء!')) return;
            fetch('{{ route("admin.lands.bulk-force-delete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ids: getSelectedIds() })
            }).then(r => r.json()).then(d => {
                if (d.success) location.reload();
                else alert(d.message);
            });
        }

        // Hierarchy Toggle
        document.querySelectorAll('.hierarchy-row').forEach(row => {
            row.addEventListener('click', function() {
                const targetId = this.dataset.toggle;
                if (!targetId) return;
                const target = document.getElementById(targetId);
                const icon = this.querySelector('.toggle-icon');
                if (target) {
                    target.classList.toggle('show');
                    icon?.classList.toggle('rotated');
                }
            });
        });

        // Cascading Dropdowns
        function loadCities(governorateId, targetId) {
            const target = document.getElementById(targetId);
            target.innerHTML = '<option value="">جاري التحميل...</option>';
            if (!governorateId) {
                target.innerHTML = '<option value="">اختر المدينة</option>';
                return;
            }
            fetch(`/lands/cities/${governorateId}`)
                .then(r => r.json())
                .then(cities => {
                    let html = '<option value="">اختر المدينة</option>';
                    cities.forEach(c => html += `<option value="${c.id}">${c.name}</option>`);
                    target.innerHTML = html;
                    if (target.dataset.selected) {
                        target.value = target.dataset.selected;
                        target.dispatchEvent(new Event('change'));
                    }
                });
        }

        function loadDistricts(cityId, targetId) {
            const target = document.getElementById(targetId);
            target.innerHTML = '<option value="">جاري التحميل...</option>';
            if (!cityId) {
                target.innerHTML = '<option value="">اختر الحي</option>';
                return;
            }
            fetch(`/lands/districts/${cityId}`)
                .then(r => r.json())
                .then(districts => {
                    let html = '<option value="">اختر الحي</option>';
                    districts.forEach(d => html += `<option value="${d.id}">${d.name}</option>`);
                    target.innerHTML = html;
                    if (target.dataset.selected) {
                        target.value = target.dataset.selected;
                        target.dispatchEvent(new Event('change'));
                    }
                });
        }

        function loadZones(districtId, targetId) {
            const target = document.getElementById(targetId);
            target.innerHTML = '<option value="">جاري التحميل...</option>';
            if (!districtId) {
                target.innerHTML = '<option value="">اختر المنطقة</option>';
                return;
            }
            fetch(`/lands/zones/${districtId}`)
                .then(r => r.json())
                .then(zones => {
                    let html = '<option value="">اختر المنطقة</option>';
                    zones.forEach(z => html += `<option value="${z.id}">${z.name}</option>`);
                    target.innerHTML = html;
                    if (target.dataset.selected) {
                        target.value = target.dataset.selected;
                        target.dispatchEvent(new Event('change'));
                    }
                });
        }

        function loadAreas(zoneId, targetId) {
            const target = document.getElementById(targetId);
            target.innerHTML = '<option value="">جاري التحميل...</option>';
            if (!zoneId) {
                target.innerHTML = '<option value="">اختر القسم</option>';
                return;
            }
            fetch(`/lands/areas/${zoneId}`)
                .then(r => r.json())
                .then(areas => {
                    let html = '<option value="">اختر القسم</option>';
                    areas.forEach(a => html += `<option value="${a.id}">${a.name}</option>`);
                    target.innerHTML = html;
                    if (target.dataset.selected) {
                        target.value = target.dataset.selected;
                    }
                });
        }
    </script>
</body>
</html>
