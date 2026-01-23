<!DOCTYPE html>
@include('admin.main.html')

<head>
    <title>مواقع التخزين - أرشيف القاهرة الجديدة</title>
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
                        <div class="mb-2 mt-3 page-title-box">
                            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between py-2 px-3 bg-body border border-secondary border-opacity-10 shadow-sm rounded-3">
                                <div>
                                    <span class="badge bg-primary-subtle text-primary fw-normal shadow-sm px-2 d-inline-flex align-items-center">
                                        <i class="ti ti-building-warehouse me-1"></i> مواقع التخزين
                                    </span>
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb mb-0 mt-1">
                                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                                            <li class="breadcrumb-item active">مواقع التخزين</li>
                                        </ol>
                                    </nav>
                                </div>
                                <div class="d-flex gap-2 mt-2 mt-lg-0">
                                    @can('physical_locations.create')
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                                        <i class="ti ti-plus me-1"></i> إضافة غرفة
                                    </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stats Cards --}}
                <div class="row g-3 mb-4">
                    <div class="col">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md bg-primary-subtle text-primary rounded">
                                        <i class="ti ti-home fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h4 class="mb-0">{{ $stats['rooms'] }}</h4>
                                        <small class="text-muted">الغرف</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md bg-success-subtle text-success rounded">
                                        <i class="ti ti-road fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h4 class="mb-0">{{ $stats['lanes'] }}</h4>
                                        <small class="text-muted">الممرات</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md bg-info-subtle text-info rounded">
                                        <i class="ti ti-layout-list fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h4 class="mb-0">{{ $stats['stands'] }}</h4>
                                        <small class="text-muted">الحوامل</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md bg-warning-subtle text-warning rounded">
                                        <i class="ti ti-archive fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h4 class="mb-0">{{ $stats['racks'] }}</h4>
                                        <small class="text-muted">الأدراج</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Data Table --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-3">
                                    <h5 class="card-title mb-0">الغرف ومواقع التخزين</h5>
                                    <span class="badge bg-primary-subtle text-primary">{{ count($rooms) }} غرفة</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
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
                            <div class="card-body p-0">
                                {{-- List View --}}
                                <div id="listView" class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>#</th>
                                                <th>المبنى</th>
                                                <th>الغرفة</th>
                                                <th>عدد الممرات</th>
                                                <th>عدد الحوامل</th>
                                                <th>عدد الأدراج</th>
                                                <th width="180" class="text-center">الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($rooms as $room)
                                            @php
                                                $standsTotal = $room->lanes->sum(fn($l) => $l->stands->count());
                                                $racksTotal = $room->lanes->sum(fn($l) => $l->stands->sum(fn($s) => $s->racks->count()));
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td><span class="text-muted">{{ $room->building_name ?: '-' }}</span></td>
                                                <td><span class="fw-medium">{{ $room->name }}</span></td>
                                                <td><span class="badge bg-success">{{ $room->lanes->count() }}</span></td>
                                                <td><span class="badge bg-info">{{ $standsTotal }}</span></td>
                                                <td><span class="badge bg-warning">{{ $racksTotal }}</span></td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button class="btn btn-soft-info btn-sm" onclick="showRoom({{ $room->id }}, '{{ $room->name }}')" title="عرض التفاصيل">
                                                            <i class="ti ti-eye"></i>
                                                        </button>
                                                        @can('physical_locations.create')
                                                        <button class="btn btn-soft-success btn-sm" data-bs-toggle="modal" data-bs-target="#addLaneModal_{{ $room->id }}" title="إضافة ممر">
                                                            <i class="ti ti-plus"></i>
                                                        </button>
                                                        @endcan
                                                        @can('physical_locations.edit')
                                                        <button class="btn btn-soft-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editRoomModal_{{ $room->id }}" title="تعديل">
                                                            <i class="ti ti-edit"></i>
                                                        </button>
                                                        @endcan
                                                        @can('physical_locations.delete')
                                                        @if($room->lanes->count() == 0)
                                                        <form action="{{ route('admin.physical-locations.rooms.destroy', $room) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الغرفة؟')">
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
                                                <td colspan="7" class="text-center py-5">
                                                    <div class="text-muted">
                                                        <i class="ti ti-building-warehouse fs-1 d-block mb-2"></i>
                                                        <p class="mb-2">لا توجد غرف</p>
                                                        @can('physical_locations.create')
                                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                                                            <i class="ti ti-plus me-1"></i>إضافة غرفة
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
                                <div id="cardView" class="d-none p-3">
                                    <div class="row g-3">
                                        @forelse($rooms as $room)
                                        @php
                                            $standsTotal = $room->lanes->sum(fn($l) => $l->stands->count());
                                            $racksTotal = $room->lanes->sum(fn($l) => $l->stands->sum(fn($s) => $s->racks->count()));
                                        @endphp
                                        <div class="col-md-4 col-lg-3">
                                            <div class="card border shadow-sm h-100">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="avatar avatar-md bg-primary-subtle text-primary rounded-circle me-3 d-flex align-items-center justify-content-center">
                                                            <i class="ti ti-home"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-0">{{ $room->name }}</h6>
                                                            <small class="text-muted">{{ $room->building_name ?: 'بدون مبنى' }}</small>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                                        <span class="badge bg-success-subtle text-success"><i class="ti ti-road me-1"></i>{{ $room->lanes->count() }} ممر</span>
                                                        <span class="badge bg-info-subtle text-info"><i class="ti ti-layout-list me-1"></i>{{ $standsTotal }} حامل</span>
                                                        <span class="badge bg-warning-subtle text-warning"><i class="ti ti-archive me-1"></i>{{ $racksTotal }} درج</span>
                                                    </div>
                                                </div>
                                                <div class="card-footer bg-transparent border-top-0 pt-0">
                                                    <div class="d-flex justify-content-between">
                                                        <button class="btn btn-soft-info btn-sm" onclick="showRoom({{ $room->id }}, '{{ $room->name }}')"><i class="ti ti-eye"></i></button>
                                                        @can('physical_locations.create')
                                                        <button class="btn btn-soft-success btn-sm" data-bs-toggle="modal" data-bs-target="#addLaneModal_{{ $room->id }}"><i class="ti ti-plus"></i></button>
                                                        @endcan
                                                        @can('physical_locations.edit')
                                                        <button class="btn btn-soft-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editRoomModal_{{ $room->id }}"><i class="ti ti-edit"></i></button>
                                                        @endcan
                                                        @can('physical_locations.delete')
                                                        @if($room->lanes->count() == 0)
                                                        <form action="{{ route('admin.physical-locations.rooms.destroy', $room) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد؟')">
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
                                        <div class="col-12 text-center py-4">
                                            <div class="text-muted">
                                                <i class="ti ti-building-warehouse fs-1 d-block mb-2"></i>
                                                لا توجد غرف
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

    {{-- Add Room Modal --}}
    <div class="modal fade" id="addRoomModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.physical-locations.rooms.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-home me-2 text-primary"></i>إضافة غرفة جديدة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">اسم المبنى</label>
                            <input type="text" name="building_name" class="form-control" placeholder="مثال: المبنى الرئيسي">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">اسم الغرفة <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="مثال: غرفة الأرشيف 1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="وصف اختياري"></textarea>
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

    {{-- Edit Room Modals + Add Lane Modals (one per room) --}}
    @foreach($rooms as $room)
    <div class="modal fade" id="editRoomModal_{{ $room->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.physical-locations.rooms.update', $room) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-edit me-2 text-warning"></i>تعديل الغرفة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">اسم المبنى</label>
                            <input type="text" name="building_name" class="form-control" value="{{ $room->building_name }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">اسم الغرفة <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="{{ $room->name }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea name="description" class="form-control" rows="2">{{ $room->description }}</textarea>
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

    <div class="modal fade" id="addLaneModal_{{ $room->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.physical-locations.lanes.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-road me-2 text-success"></i>إضافة ممر - {{ $room->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">اسم الممر <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="مثال: الممر أ">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="وصف اختياري"></textarea>
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

    {{-- Show Room Modal (Hierarchy View) --}}
    <div class="modal fade" id="showRoomModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm bg-primary-subtle text-primary rounded me-2">
                            <i class="ti ti-building-warehouse"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0 fw-semibold" id="showRoomName"></h5>
                            <small class="text-muted">هيكل التخزين</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="refreshRoomData()" title="تحديث">
                            <i class="ti ti-refresh"></i>
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                </div>
                <div class="modal-body p-0" style="max-height: 70vh; overflow-y: auto;">
                    <div id="showRoomBody">
                        <div class="text-center py-5">
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
    <input type="hidden" id="currentRoomId">

    {{-- Add Stand Modal --}}
    <div class="modal fade" id="addStandModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.physical-locations.stands.store') }}" method="POST" id="addStandForm">
                    @csrf
                    <input type="hidden" name="lane_id" id="addStandLaneId">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-layout-list me-2 text-info"></i>إضافة حامل - <span id="addStandLaneName"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">اسم الحامل <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="مثال: الحامل 1">
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

    {{-- Add Rack Modal --}}
    <div class="modal fade" id="addRackModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.physical-locations.racks.store') }}" method="POST" id="addRackForm">
                    @csrf
                    <input type="hidden" name="stand_id" id="addRackStandId">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-archive me-2 text-warning"></i>إضافة درج - <span id="addRackStandName"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">اسم الدرج <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="مثال: الدرج 1">
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

    {{-- Edit Lane Modal --}}
    <div class="modal fade" id="editLaneModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editLaneForm" method="POST">
                    @csrf
                    <input type="hidden" name="room_id" id="editLaneRoomId">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-edit me-2 text-warning"></i>تعديل الممر</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">اسم الممر <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editLaneName" class="form-control" required>
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

    {{-- Edit Stand Modal --}}
    <div class="modal fade" id="editStandModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editStandForm" method="POST">
                    @csrf
                    <input type="hidden" name="lane_id" id="editStandLaneId">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-edit me-2 text-warning"></i>تعديل الحامل</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">اسم الحامل <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editStandName" class="form-control" required>
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

    {{-- Edit Rack Modal --}}
    <div class="modal fade" id="editRackModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editRackForm" method="POST">
                    @csrf
                    <input type="hidden" name="stand_id" id="editRackStandId">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-edit me-2 text-warning"></i>تعديل الدرج</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">اسم الدرج <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editRackName" class="form-control" required>
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
            localStorage.setItem('physicalLocationsView', view);
        }

        // Show Room
        function showRoom(id, name) {
            document.getElementById('showRoomName').textContent = name;
            document.getElementById('currentRoomId').value = id;
            document.getElementById('showRoomBody').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div><p class="mt-3 text-muted">جاري تحميل البيانات...</p></div>';
            new bootstrap.Modal(document.getElementById('showRoomModal')).show();
            loadRoomData(id);
        }

        function loadRoomData(id) {
            fetch(`/physical-locations/rooms/${id}/show`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderLanesList(data.room);
                } else {
                    document.getElementById('showRoomBody').innerHTML = '<div class="alert alert-danger m-3">حدث خطأ أثناء التحميل</div>';
                }
            })
            .catch(err => {
                console.error('Error:', err);
                document.getElementById('showRoomBody').innerHTML = '<div class="alert alert-danger m-3">حدث خطأ في الاتصال</div>';
            });
        }

        function refreshRoomData() {
            const id = document.getElementById('currentRoomId').value;
            if (id) {
                document.getElementById('showRoomBody').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-3 text-muted">جاري تحديث البيانات...</p></div>';
                loadRoomData(id);
            }
        }

        function renderLanesList(room) {
            const lanes = room.lanes || [];
            if (lanes.length === 0) {
                document.getElementById('showRoomBody').innerHTML = `
                <div class="text-center py-5">
                    <div class="avatar avatar-lg bg-dark-subtle text-dark rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:64px;height:64px;">
                        <i class="ti ti-road fs-2"></i>
                    </div>
                    <h6 class="text-muted mb-3">لا توجد ممرات مضافة</h6>
                </div>`;
                return;
            }

            let totalStands = 0, totalRacks = 0;
            lanes.forEach(l => {
                const stands = l.stands || [];
                totalStands += stands.length;
                stands.forEach(s => { totalRacks += (s.racks || []).length; });
            });

            let html = `
            <div class="bg-light border-bottom p-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex flex-wrap gap-2">
                        <div class="bg-white rounded px-3 py-2 shadow-sm">
                            <span class="fs-5 fw-bold text-success">${lanes.length}</span>
                            <span class="text-muted small ms-1">ممر</span>
                        </div>
                        <div class="bg-white rounded px-3 py-2 shadow-sm">
                            <span class="fs-5 fw-bold text-info">${totalStands}</span>
                            <span class="text-muted small ms-1">حامل</span>
                        </div>
                        <div class="bg-white rounded px-3 py-2 shadow-sm">
                            <span class="fs-5 fw-bold text-warning">${totalRacks}</span>
                            <span class="text-muted small ms-1">درج</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-3">`;

            lanes.forEach((lane, idx) => {
                const stands = lane.stands || [];
                html += `
                <div class="card mb-2 border shadow-sm">
                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center" style="cursor:pointer" data-bs-toggle="collapse" data-bs-target="#lane${lane.id}">
                            <i class="ti ti-chevron-down me-2 text-success"></i>
                            <i class="ti ti-road text-success me-2 fs-5"></i>
                            <strong class="text-success">${lane.name}</strong>
                            <span class="badge bg-success ms-2">${stands.length} حامل</span>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-soft-info btn-sm" onclick="addStand(${lane.id}, '${lane.name}')" title="إضافة حامل"><i class="ti ti-plus"></i></button>
                            <button class="btn btn-soft-warning btn-sm" onclick="editLane(${lane.id}, '${lane.name}', ${room.id})" title="تعديل"><i class="ti ti-edit"></i></button>
                            ${stands.length === 0 ? `<button class="btn btn-soft-danger btn-sm" onclick="deleteLane(${lane.id})" title="حذف"><i class="ti ti-trash"></i></button>` : ''}
                        </div>
                    </div>
                    <div class="collapse ${idx === 0 ? 'show' : ''}" id="lane${lane.id}">
                        <div class="card-body py-2">
                            ${stands.length > 0 ? stands.map(stand => {
                                const racks = stand.racks || [];
                                return `
                                <div class="card mb-2 border-0 bg-light">
                                    <div class="card-header bg-transparent py-2 d-flex justify-content-between align-items-center border-0">
                                        <div class="d-flex align-items-center" style="cursor:pointer" data-bs-toggle="collapse" data-bs-target="#stand${stand.id}">
                                            <i class="ti ti-chevron-down me-2 text-info"></i>
                                            <i class="ti ti-layout-list text-info me-2"></i>
                                            <span class="fw-medium text-info">${stand.name}</span>
                                            <span class="badge bg-info ms-2">${racks.length} درج</span>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-soft-warning btn-sm" onclick="addRack(${stand.id}, '${stand.name}')" title="إضافة درج"><i class="ti ti-plus"></i></button>
                                            <button class="btn btn-soft-warning btn-sm" onclick="editStand(${stand.id}, '${stand.name}', ${lane.id})" title="تعديل"><i class="ti ti-edit"></i></button>
                                            ${racks.length === 0 ? `<button class="btn btn-soft-danger btn-sm" onclick="deleteStand(${stand.id})" title="حذف"><i class="ti ti-trash"></i></button>` : ''}
                                        </div>
                                    </div>
                                    <div class="collapse" id="stand${stand.id}">
                                        <div class="card-body py-2">
                                            ${racks.length > 0 ? `
                                                <div class="row g-2">
                                                    ${racks.map(rack => `
                                                        <div class="col-md-6 col-lg-4">
                                                            <div class="d-flex justify-content-between align-items-center p-2 bg-warning-subtle rounded">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="ti ti-archive text-warning me-2"></i>
                                                                    <span class="fw-medium">${rack.name}</span>
                                                                    ${rack.files_count > 0 ? `<span class="badge bg-secondary ms-2">${rack.files_count} ملف</span>` : ''}
                                                                </div>
                                                                <div class="btn-group btn-group-sm">
                                                                    <button class="btn btn-soft-warning btn-sm" onclick="editRack(${rack.id}, '${rack.name}', ${stand.id})" title="تعديل"><i class="ti ti-edit"></i></button>
                                                                    ${(rack.files_count || 0) === 0 ? `<button class="btn btn-soft-danger btn-sm" onclick="deleteRack(${rack.id})" title="حذف"><i class="ti ti-trash"></i></button>` : ''}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    `).join('')}
                                                </div>
                                            ` : '<p class="text-muted small mb-0">لا توجد أدراج</p>'}
                                        </div>
                                    </div>
                                </div>`;
                            }).join('') : '<p class="text-muted small mb-0">لا توجد حوامل</p>'}
                        </div>
                    </div>
                </div>`;
            });
            html += '</div>';
            document.getElementById('showRoomBody').innerHTML = html;
        }

        // Add Functions
        function addStand(laneId, laneName) {
            document.getElementById('addStandLaneId').value = laneId;
            document.getElementById('addStandLaneName').textContent = laneName;
            new bootstrap.Modal(document.getElementById('addStandModal')).show();
        }

        function addRack(standId, standName) {
            document.getElementById('addRackStandId').value = standId;
            document.getElementById('addRackStandName').textContent = standName;
            new bootstrap.Modal(document.getElementById('addRackModal')).show();
        }

        // Edit Functions
        function editLane(id, name, roomId) {
            document.getElementById('editLaneForm').action = `/physical-locations/lanes/${id}`;
            document.getElementById('editLaneName').value = name;
            document.getElementById('editLaneRoomId').value = roomId;
            new bootstrap.Modal(document.getElementById('editLaneModal')).show();
        }

        function editStand(id, name, laneId) {
            document.getElementById('editStandForm').action = `/physical-locations/stands/${id}`;
            document.getElementById('editStandName').value = name;
            document.getElementById('editStandLaneId').value = laneId;
            new bootstrap.Modal(document.getElementById('editStandModal')).show();
        }

        function editRack(id, name, standId) {
            document.getElementById('editRackForm').action = `/physical-locations/racks/${id}`;
            document.getElementById('editRackName').value = name;
            document.getElementById('editRackStandId').value = standId;
            new bootstrap.Modal(document.getElementById('editRackModal')).show();
        }

        // Delete Functions
        function deleteLane(id) {
            if (confirm('هل أنت متأكد من حذف هذا الممر؟')) {
                submitDelete(`/physical-locations/lanes/${id}/delete`);
            }
        }

        function deleteStand(id) {
            if (confirm('هل أنت متأكد من حذف هذا الحامل؟')) {
                submitDelete(`/physical-locations/stands/${id}/delete`);
            }
        }

        function deleteRack(id) {
            if (confirm('هل أنت متأكد من حذف هذا الدرج؟')) {
                submitDelete(`/physical-locations/racks/${id}/delete`);
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
                    refreshRoomData();
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
            const savedView = localStorage.getItem('physicalLocationsView') || 'list';
            toggleView(savedView);
        });
    </script>
</body>
</html>
