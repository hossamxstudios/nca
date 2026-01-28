<!DOCTYPE html>
@include('admin.main.html')

<head>
    <title>المناطق الجغرافية - أرشيف القاهرة الجديدة</title>
    @include('admin.main.meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div class="wrapper">
        @include('admin.main.topbar')
        @include('admin.main.sidebar')
        <div class="content-page">
            <div class="container-fluid">
                {{-- Page Header --}}
                <div class="row">
                    <div class="col-12">
                        <div class="mt-3 mb-2 page-title-box">
                            <div class="px-3 py-2 border border-opacity-10 shadow-sm d-flex flex-column flex-lg-row align-items-lg-center justify-content-between bg-body border-secondary rounded-3">
                                <div>
                                    <span class="px-2 shadow-sm badge bg-primary-subtle text-primary fw-normal d-inline-flex align-items-center">
                                        <i class="ti ti-map-pin me-1"></i> المناطق الجغرافية
                                    </span>
                                    <nav aria-label="breadcrumb">
                                        <ol class="mt-1 mb-0 breadcrumb">
                                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                                            <li class="breadcrumb-item active">المناطق الجغرافية</li>
                                        </ol>
                                    </nav>
                                </div>
                                <div class="gap-2 mt-2 d-flex mt-lg-0">
                                    @can('geographic_areas.create')
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGovernorateModal">
                                        <i class="ti ti-plus me-1"></i> إضافة محافظة
                                    </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stats Cards --}}
                <div class="mb-4 row g-3">
                    <div class="col">
                        <div class="border-0 shadow-sm card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="rounded avatar avatar-md bg-primary-subtle text-primary">
                                        <i class="ti ti-building fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h4 class="mb-0">{{ $stats['governorates'] }}</h4>
                                        <span class="text-dark">المحافظات</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="border-0 shadow-sm card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="rounded avatar avatar-md bg-success-subtle text-success">
                                        <i class="ti ti-building-community fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h4 class="mb-0">{{ $stats['cities'] }}</h4>
                                        <span class="text-dark">المدن</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="border-0 shadow-sm card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="rounded avatar avatar-md bg-info-subtle text-info">
                                        <i class="ti ti-map-2 fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h4 class="mb-0">{{ $stats['districts'] }}</h4>
                                        <span class="text-dark">الأحياء</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="border-0 shadow-sm card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="rounded avatar avatar-md bg-warning-subtle text-warning">
                                        <i class="ti ti-map-pin fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h4 class="mb-0">{{ $stats['zones'] }}</h4>
                                        <span class="text-dark">المناطق</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="border-0 shadow-sm card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="rounded avatar avatar-md bg-danger-subtle text-danger">
                                        <i class="ti ti-location fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h4 class="mb-0">{{ $stats['areas'] }}</h4>
                                        <span class="text-dark">الأقسام</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Data Table --}}
                <div class="rounded row">
                    <div class="col-12 card">
                        <div class="rounded border-0 shadow">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="gap-3 d-flex align-items-center">
                                    <h5 class="mb-0 card-title">المحافظات والمدن</h5>
                                    <span class="badge bg-primary-subtle text-primary fs-6">{{ count($governorates) }} محافظة</span>
                                </div>
                                <div class="gap-2 d-flex align-items-center">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary active" id="listViewBtn" onclick="toggleView('list')">
                                            <i class="ti ti-list"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="cardViewBtn" onclick="toggleView('card')">
                                            <i class="ti ti-layout-grid"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="p-0 card-body">
                                {{-- List View --}}
                                <div id="listView" class="table-responsive">
                                    <table class="table mb-0 table-hover">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>#</th>
                                                <th>المحافظة</th>
                                                <th>عدد المدن</th>
                                                <th>عدد الأحياء</th>
                                                <th>عدد المناطق</th>
                                                <th>عدد الأقسام</th>
                                                <th width="180" class="text-center">الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($governorates as $governorate)
                                            @php
                                                $districtsTotal = $governorate->cities->sum(fn($c) => $c->districts->count());
                                                $zonesTotal = $governorate->cities->sum(fn($c) => $c->districts->sum(fn($d) => $d->zones->count()));
                                                $areasTotal = $governorate->cities->sum(fn($c) => $c->districts->sum(fn($d) => $d->zones->sum(fn($z) => $z->areas->count())));
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <span class="fw-medium">{{ $governorate->name }}</span>
                                                </td>
                                                <td><span class="badge bg-primary fs-5">{{ $governorate->cities->count() }}</span></td>
                                                <td><span class="badge bg-primary fs-5">{{ $districtsTotal }}</span></td>
                                                <td><span class="badge bg-primary fs-5">{{ $zonesTotal }}</span></td>
                                                <td><span class="badge bg-primary fs-5">{{ $areasTotal }}</span></td>
                                                <td class="text-center">
                                                    <div class="gap-1 d-flex justify-content-center">
                                                        <button class="btn btn-soft-primary btn-sm" onclick="showGovernorate({{ $governorate->id }}, '{{ $governorate->name }}')" title="عرض التفاصيل">
                                                            <i class="ti ti-eye"></i>
                                                        </button>
                                                        @can('geographic_areas.create')
                                                        <button class="btn btn-soft-success btn-sm" data-bs-toggle="modal" data-bs-target="#addCityModal_{{ $governorate->id }}" title="إضافة مدينة">
                                                            <i class="ti ti-plus"></i>
                                                        </button>
                                                        @endcan
                                                        @can('geographic_areas.edit')
                                                        <button class="btn btn-soft-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editGovernorateModal_{{ $governorate->id }}" title="تعديل">
                                                            <i class="ti ti-edit"></i>
                                                        </button>
                                                        @endcan
                                                        @can('geographic_areas.delete')
                                                        @if($governorate->cities->count() == 0)
                                                        <form action="{{ route('admin.geographic-areas.governorates.destroy', $governorate) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المحافظة؟')">
                                                            @csrf
                                                            <button class="btn btn-soft-danger btn-sm" title="حذف">
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </form>
                                                        @endif
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="py-5 text-center">
                                                    <div class="text-muted">
                                                        <i class="mb-2 ti ti-map-off fs-1 d-block"></i>
                                                        <p class="mb-2">لا توجد محافظات</p>
                                                        @can('geographic_areas.create')
                                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addGovernorateModal">
                                                            <i class="ti ti-plus me-1"></i>إضافة محافظة
                                                        </button>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Card View --}}
                                <div id="cardView" class="p-3 d-none">
                                    <div class="row g-3">
                                        @forelse($governorates as $governorate)
                                        @php
                                            $districtsTotal = $governorate->cities->sum(fn($c) => $c->districts->count());
                                            $zonesTotal = $governorate->cities->sum(fn($c) => $c->districts->sum(fn($d) => $d->zones->count()));
                                            $areasTotal = $governorate->cities->sum(fn($c) => $c->districts->sum(fn($d) => $d->zones->sum(fn($z) => $z->areas->count())));
                                        @endphp
                                        <div class="col-md-4 col-lg-3">
                                            <div class="border shadow-sm card h-100">
                                                <div class="card-body">
                                                    <div class="mb-3 d-flex align-items-center">
                                                        <div class="avatar avatar-md bg-primary-subtle text-primary rounded-circle me-3 d-flex align-items-center justify-content-center">
                                                            <i class="ti ti-building"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-0">{{ $governorate->name }}</h6>
                                                        </div>
                                                    </div>
                                                    <div class="flex-wrap gap-2 mb-3 d-flex">
                                                        <span class="badge bg-success-subtle text-success"><i class="ti ti-building-community me-1"></i>{{ $governorate->cities->count() }} مدينة</span>
                                                        <span class="badge bg-info-subtle text-info"><i class="ti ti-map-2 me-1"></i>{{ $districtsTotal }} حي</span>
                                                        <span class="badge bg-warning-subtle text-warning"><i class="ti ti-map-pin me-1"></i>{{ $zonesTotal }} منطقة</span>
                                                    </div>
                                                </div>
                                                <div class="pt-0 bg-transparent card-footer border-top-0">
                                                    <div class="d-flex justify-content-between">
                                                        <button class="btn btn-soft-info btn-sm" onclick="showGovernorate({{ $governorate->id }}, '{{ $governorate->name }}')"><i class="ti ti-eye"></i></button>
                                                        @can('geographic_areas.create')
                                                        <button class="btn btn-soft-success btn-sm" data-bs-toggle="modal" data-bs-target="#addCityModal_{{ $governorate->id }}"><i class="ti ti-plus"></i></button>
                                                        @endcan
                                                        @can('geographic_areas.edit')
                                                        <button class="btn btn-soft-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editGovernorateModal_{{ $governorate->id }}"><i class="ti ti-edit"></i></button>
                                                        @endcan
                                                        @can('geographic_areas.delete')
                                                        @if($governorate->cities->count() == 0)
                                                        <form action="{{ route('admin.geographic-areas.governorates.destroy', $governorate) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد؟')">
                                                            @csrf
                                                            <button class="btn btn-soft-danger btn-sm"><i class="ti ti-trash"></i></button>
                                                        </form>
                                                        @endif
                                                        @endcan
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="py-4 text-center col-12">
                                            <div class="text-muted">
                                                <i class="mb-2 ti ti-map-off fs-1 d-block"></i>
                                                لا توجد محافظات
                                            </div>
                                        </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Governorate Modal --}}
    <div class="modal fade" id="addGovernorateModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.geographic-areas.governorates.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-building me-2 text-primary"></i>إضافة محافظة جديدة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">اسم المحافظة <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="مثال: القاهرة">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Governorate Modals (one per governorate) --}}
    @foreach($governorates as $governorate)
    <div class="modal fade" id="editGovernorateModal_{{ $governorate->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.geographic-areas.governorates.update', $governorate) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-edit me-2 text-warning"></i>تعديل المحافظة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">اسم المحافظة <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="{{ $governorate->name }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-warning">تحديث</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add City Modal (one per governorate) --}}
    <div class="modal fade" id="addCityModal_{{ $governorate->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.geographic-areas.cities.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="governorate_id" value="{{ $governorate->id }}">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-building-community me-2 text-success"></i>إضافة مدينة - {{ $governorate->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">اسم المدينة <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="مثال: القاهرة الجديدة">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Show Governorate Modal (Hierarchy View) --}}
    <div class="modal fade" id="showGovernorateModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="border-0 shadow modal-content">
                <div class="modal-header border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="rounded avatar avatar-sm bg-primary-subtle text-primary me-2">
                            <i class="ti ti-map-pin"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 modal-title fw-semibold" id="showGovName"></h5>
                            <small class="text-muted">التقسيم الجغرافي</small>
                        </div>
                    </div>
                    <div class="gap-2 d-flex align-items-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="refreshGovernorateData()" title="تحديث">
                            <i class="ti ti-refresh"></i>
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                </div>
                <div class="p-0 modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div id="showGovBody">
                        <div class="py-5 text-center">
                            <div class="spinner-border text-primary"></div>
                            <p class="mt-3 text-muted">جاري التحميل...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="currentGovernorateId">

    {{-- Add District Modal --}}
    <div class="modal fade" id="addDistrictModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.geographic-areas.districts.store') }}" method="POST" id="addDistrictForm">
                    @csrf
                    <input type="hidden" name="city_id" id="addDistrictCityId">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-map-2 me-2 text-info"></i>إضافة حي - <span id="addDistrictCityName"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">اسم الحي <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="مثال: التجمع الخامس">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-info">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Zone Modal --}}
    <div class="modal fade" id="addZoneModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.geographic-areas.zones.store') }}" method="POST" id="addZoneForm">
                    @csrf
                    <input type="hidden" name="district_id" id="addZoneDistrictId">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-map-pin me-2 text-warning"></i>إضافة منطقة - <span id="addZoneDistrictName"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">اسم المنطقة <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="مثال: الحي الأول">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-warning">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Area Modal --}}
    <div class="modal fade" id="addAreaModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.geographic-areas.areas.store') }}" method="POST" id="addAreaForm">
                    @csrf
                    <input type="hidden" name="zone_id" id="addAreaZoneId">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-location me-2 text-danger"></i>إضافة قسم - <span id="addAreaZoneName"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">اسم القسم <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="مثال: القسم أ">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit City Modal --}}
    <div class="modal fade" id="editCityModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editCityForm" method="POST">
                    @csrf
                    <input type="hidden" name="governorate_id" id="editCityGovernorateId">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-edit me-2 text-warning"></i>تعديل المدينة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">اسم المدينة <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editCityName" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-warning">تحديث</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit District Modal --}}
    <div class="modal fade" id="editDistrictModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editDistrictForm" method="POST">
                    @csrf
                    <input type="hidden" name="city_id" id="editDistrictCityId">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-edit me-2 text-warning"></i>تعديل الحي</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">اسم الحي <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editDistrictName" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-warning">تحديث</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Zone Modal --}}
    <div class="modal fade" id="editZoneModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editZoneForm" method="POST">
                    @csrf
                    <input type="hidden" name="district_id" id="editZoneDistrictId">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-edit me-2 text-warning"></i>تعديل المنطقة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">اسم المنطقة <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editZoneName" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-warning">تحديث</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Area Modal --}}
    <div class="modal fade" id="editAreaModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editAreaForm" method="POST">
                    @csrf
                    <input type="hidden" name="zone_id" id="editAreaZoneId">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-edit me-2 text-warning"></i>تعديل القسم</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">اسم القسم <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editAreaName" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-warning">تحديث</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('admin.main.scripts')

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Toggle View
        function toggleView(view) {
            const listView = document.getElementById('listView');
            const cardView = document.getElementById('cardView');
            const listBtn = document.getElementById('listViewBtn');
            const cardBtn = document.getElementById('cardViewBtn');

            if (view === 'list') {
                listView.classList.remove('d-none');
                cardView.classList.add('d-none');
                listBtn.classList.add('active');
                cardBtn.classList.remove('active');
            } else {
                listView.classList.add('d-none');
                cardView.classList.remove('d-none');
                listBtn.classList.remove('active');
                cardBtn.classList.add('active');
            }
            localStorage.setItem('geoAreasView', view);
        }

        // Show Governorate
        function showGovernorate(id, name) {
            document.getElementById('showGovName').textContent = name;
            document.getElementById('currentGovernorateId').value = id;
            document.getElementById('showGovBody').innerHTML = '<div class="py-5 text-center"><div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div><p class="mt-3 text-muted">جاري تحميل البيانات...</p></div>';
            new bootstrap.Modal(document.getElementById('showGovernorateModal')).show();
            loadGovernorateData(id);
        }

        function loadGovernorateData(id) {
            fetch(`/geographic-areas/governorates/${id}/show`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderCitiesList(data.governorate);
                } else {
                    document.getElementById('showGovBody').innerHTML = '<div class="m-3 alert alert-danger">حدث خطأ أثناء التحميل</div>';
                }
            })
            .catch(err => {
                console.error('Error:', err);
                document.getElementById('showGovBody').innerHTML = '<div class="m-3 alert alert-danger">حدث خطأ في الاتصال</div>';
            });
        }

        function refreshGovernorateData() {
            const id = document.getElementById('currentGovernorateId').value;
            if (id) {
                document.getElementById('showGovBody').innerHTML = '<div class="py-5 text-center"><div class="spinner-border text-primary"></div><p class="mt-3 text-muted">جاري تحديث البيانات...</p></div>';
                loadGovernorateData(id);
            }
        }

        function renderCitiesList(gov) {
            const cities = gov.cities || [];
            if (cities.length === 0) {
                document.getElementById('showGovBody').innerHTML = `
                <div class="py-5 text-center">
                    <div class="mx-auto mb-3 avatar avatar-lg bg-dark-subtle text-dark rounded-circle d-flex align-items-center justify-content-center" style="width:64px;height:64px;">
                        <i class="ti ti-building-community fs-2"></i>
                    </div>
                    <h6 class="mb-3 text-muted">لا توجد مدن مضافة</h6>
                </div>`;
                return;
            }

            let totalDistricts = 0, totalZones = 0, totalAreas = 0;
            cities.forEach(c => {
                const districts = c.districts || [];
                totalDistricts += districts.length;
                districts.forEach(d => {
                    const zones = d.zones || [];
                    totalZones += zones.length;
                    zones.forEach(z => { totalAreas += (z.areas || []).length; });
                });
            });

            let html = `
            <div class="p-3 bg-light border-bottom">
                <div class="flex-wrap gap-2 d-flex justify-content-between align-items-center">
                    <div class="flex-wrap gap-2 d-flex">
                        <div class="px-3 py-2 bg-white rounded shadow-sm">
                            <span class="fs-5 fw-bold text-success">${cities.length}</span>
                            <span class="text-muted small ms-1">مدينة</span>
                        </div>
                        <div class="px-3 py-2 bg-white rounded shadow-sm">
                            <span class="fs-5 fw-bold text-info">${totalDistricts}</span>
                            <span class="text-muted small ms-1">حي</span>
                        </div>
                        <div class="px-3 py-2 bg-white rounded shadow-sm">
                            <span class="fs-5 fw-bold text-warning">${totalZones}</span>
                            <span class="text-muted small ms-1">منطقة</span>
                        </div>
                        <div class="px-3 py-2 bg-white rounded shadow-sm">
                            <span class="fs-5 fw-bold text-danger">${totalAreas}</span>
                            <span class="text-muted small ms-1">قسم</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-3">`;

            cities.forEach((city, idx) => {
                const districts = city.districts || [];
                html += `
                <div class="mb-2 border shadow-sm card">
                    <div class="py-2 card-header bg-light d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center" style="cursor:pointer" data-bs-toggle="collapse" data-bs-target="#city${city.id}">
                            <i class="ti ti-chevron-down me-2 text-success"></i>
                            <i class="ti ti-building-community text-success me-2 fs-5"></i>
                            <strong class="text-success">${city.name}</strong>
                            <span class="badge bg-success ms-2">${districts.length} حي</span>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-soft-info btn-sm" onclick="addDistrict(${city.id}, '${city.name}')" title="إضافة حي"><i class="ti ti-plus"></i></button>
                            <button class="btn btn-soft-warning btn-sm" onclick="editCity(${city.id}, '${city.name}', ${gov.id})" title="تعديل"><i class="ti ti-edit"></i></button>
                            ${districts.length === 0 ? `<button class="btn btn-soft-danger btn-sm" onclick="deleteCity(${city.id})" title="حذف"><i class="ti ti-trash"></i></button>` : ''}
                        </div>
                    </div>
                    <div class="collapse ${idx === 0 ? 'show' : ''}" id="city${city.id}">
                        <div class="py-2 card-body">
                            ${districts.length > 0 ? districts.map(district => {
                                const zones = district.zones || [];
                                return `
                                <div class="mb-2 border-0 card bg-light">
                                    <div class="py-2 bg-transparent border-0 card-header d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center" style="cursor:pointer" data-bs-toggle="collapse" data-bs-target="#district${district.id}">
                                            <i class="ti ti-chevron-down me-2 text-info"></i>
                                            <i class="ti ti-map-2 text-info me-2"></i>
                                            <span class="fw-medium text-info">${district.name}</span>
                                            <span class="badge bg-info ms-2">${zones.length} منطقة</span>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-soft-warning btn-sm" onclick="addZone(${district.id}, '${district.name}')" title="إضافة منطقة"><i class="ti ti-plus"></i></button>
                                            <button class="btn btn-soft-warning btn-sm" onclick="editDistrict(${district.id}, '${district.name}', ${city.id})" title="تعديل"><i class="ti ti-edit"></i></button>
                                            ${zones.length === 0 ? `<button class="btn btn-soft-danger btn-sm" onclick="deleteDistrict(${district.id})" title="حذف"><i class="ti ti-trash"></i></button>` : ''}
                                        </div>
                                    </div>
                                    <div class="collapse" id="district${district.id}">
                                        <div class="py-2 card-body">
                                            ${zones.length > 0 ? zones.map(zone => {
                                                const areas = zone.areas || [];
                                                return `
                                                <div class="mb-2 border-0 card">
                                                    <div class="py-2 rounded border-0 card-header bg-warning-subtle d-flex justify-content-between align-items-center">
                                                        <div class="d-flex align-items-center" style="cursor:pointer" data-bs-toggle="collapse" data-bs-target="#zone${zone.id}">
                                                            <i class="ti ti-chevron-down me-2 text-warning"></i>
                                                            <i class="ti ti-map-pin text-warning me-2"></i>
                                                            <span class="fw-medium text-warning">${zone.name}</span>
                                                            <span class="badge bg-warning ms-2">${areas.length} قسم</span>
                                                        </div>
                                                        <div class="btn-group btn-group-sm">
                                                            <button class="btn btn-soft-danger btn-sm" onclick="addArea(${zone.id}, '${zone.name}')" title="إضافة قسم"><i class="ti ti-plus"></i></button>
                                                            <button class="btn btn-soft-warning btn-sm" onclick="editZone(${zone.id}, '${zone.name}', ${district.id})" title="تعديل"><i class="ti ti-edit"></i></button>
                                                            ${areas.length === 0 ? `<button class="btn btn-soft-danger btn-sm" onclick="deleteZone(${zone.id})" title="حذف"><i class="ti ti-trash"></i></button>` : ''}
                                                        </div>
                                                    </div>
                                                    <div class="collapse" id="zone${zone.id}">
                                                        <div class="py-2 card-body">
                                                            ${areas.length > 0 ? `
                                                                <div class="row g-2">
                                                                    ${areas.map(area => `
                                                                        <div class="col-md-6 col-lg-4">
                                                                            <div class="p-2 rounded d-flex justify-content-between align-items-center bg-danger-subtle">
                                                                                <div class="d-flex align-items-center">
                                                                                    <i class="ti ti-location text-danger me-2"></i>
                                                                                    <span class="fw-medium">${area.name}</span>
                                                                                </div>
                                                                                <div class="btn-group btn-group-sm">
                                                                                    <button class="btn btn-soft-warning btn-sm" onclick="editArea(${area.id}, '${area.name}', ${zone.id})" title="تعديل"><i class="ti ti-edit"></i></button>
                                                                                    <button class="btn btn-soft-danger btn-sm" onclick="deleteArea(${area.id})" title="حذف"><i class="ti ti-trash"></i></button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    `).join('')}
                                                                </div>
                                                            ` : '<p class="mb-0 text-muted small">لا توجد أقسام</p>'}
                                                        </div>
                                                    </div>
                                                </div>`;
                                            }).join('') : '<p class="mb-0 text-muted small">لا توجد مناطق</p>'}
                                        </div>
                                    </div>
                                </div>`;
                            }).join('') : '<p class="mb-0 text-muted small">لا توجد أحياء</p>'}
                        </div>
                    </div>
                </div>`;
            });
            html += '</div>';
            document.getElementById('showGovBody').innerHTML = html;
        }

        // Add Functions (open modals with hidden field values)
        function addDistrict(cityId, cityName) {
            document.getElementById('addDistrictCityId').value = cityId;
            document.getElementById('addDistrictCityName').textContent = cityName;
            new bootstrap.Modal(document.getElementById('addDistrictModal')).show();
        }

        function addZone(districtId, districtName) {
            document.getElementById('addZoneDistrictId').value = districtId;
            document.getElementById('addZoneDistrictName').textContent = districtName;
            new bootstrap.Modal(document.getElementById('addZoneModal')).show();
        }

        function addArea(zoneId, zoneName) {
            document.getElementById('addAreaZoneId').value = zoneId;
            document.getElementById('addAreaZoneName').textContent = zoneName;
            new bootstrap.Modal(document.getElementById('addAreaModal')).show();
        }

        // Edit Functions
        function editCity(id, name, govId) {
            document.getElementById('editCityForm').action = `/geographic-areas/cities/${id}`;
            document.getElementById('editCityName').value = name;
            document.getElementById('editCityGovernorateId').value = govId;
            new bootstrap.Modal(document.getElementById('editCityModal')).show();
        }

        function editDistrict(id, name, cityId) {
            document.getElementById('editDistrictForm').action = `/geographic-areas/districts/${id}`;
            document.getElementById('editDistrictName').value = name;
            document.getElementById('editDistrictCityId').value = cityId;
            new bootstrap.Modal(document.getElementById('editDistrictModal')).show();
        }

        function editZone(id, name, districtId) {
            document.getElementById('editZoneForm').action = `/geographic-areas/zones/${id}`;
            document.getElementById('editZoneName').value = name;
            document.getElementById('editZoneDistrictId').value = districtId;
            new bootstrap.Modal(document.getElementById('editZoneModal')).show();
        }

        function editArea(id, name, zoneId) {
            document.getElementById('editAreaForm').action = `/geographic-areas/areas/${id}`;
            document.getElementById('editAreaName').value = name;
            document.getElementById('editAreaZoneId').value = zoneId;
            new bootstrap.Modal(document.getElementById('editAreaModal')).show();
        }

        // Delete Functions
        function deleteCity(id) {
            if (confirm('هل أنت متأكد من حذف هذه المدينة؟')) {
                submitDelete(`/geographic-areas/cities/${id}/delete`);
            }
        }

        function deleteDistrict(id) {
            if (confirm('هل أنت متأكد من حذف هذا الحي؟')) {
                submitDelete(`/geographic-areas/districts/${id}/delete`);
            }
        }

        function deleteZone(id) {
            if (confirm('هل أنت متأكد من حذف هذه المنطقة؟')) {
                submitDelete(`/geographic-areas/zones/${id}/delete`);
            }
        }

        function deleteArea(id) {
            if (confirm('هل أنت متأكد من حذف هذا القسم؟')) {
                submitDelete(`/geographic-areas/areas/${id}/delete`);
            }
        }

        function submitDelete(url) {
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success || data.message) {
                    refreshGovernorateData();
                } else {
                    alert(data.error || 'حدث خطأ');
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('حدث خطأ في الاتصال');
            });
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            const savedView = localStorage.getItem('geoAreasView') || 'list';
            toggleView(savedView);
        });
    </script>
</body>
</html>
