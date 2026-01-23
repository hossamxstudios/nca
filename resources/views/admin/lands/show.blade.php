<!DOCTYPE html>
@include('admin.main.html')

<head>
    <title>تفاصيل الأرض - أرشيف القاهرة الجديدة</title>
    @include('admin.main.meta')
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
                        <h4 class="mb-1 page-title">تفاصيل الأرض</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="p-0 mb-0 breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.lands.index') }}">الأراضي</a></li>
                                <li class="breadcrumb-item active">{{ $land->land_no }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="gap-2 d-flex">
                        <a href="{{ route('admin.lands.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-right me-1"></i>العودة
                        </a>
                        @can('lands.edit')
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editLandModal">
                            <i class="ti ti-edit me-1"></i>تعديل
                        </button>
                        @endcan
                    </div>
                </div>

                <div class="row">
                    {{-- Main Info Card --}}
                    <div class="col-lg-8">
                        <div class="mb-4 border-0 shadow-sm card">
                            <div class="card-header">
                                <h5 class="mb-0 card-title"><i class="ti ti-info-circle me-2"></i>معلومات الأرض</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="mb-1 text-muted small">العميل</label>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle avatar-sm bg-primary-subtle d-flex align-items-center justify-content-center me-2">
                                                <i class="ti ti-user text-primary"></i>
                                            </div>
                                            <a href="{{ route('admin.clients.show', $land->client_id) }}" class="fw-medium text-decoration-none">
                                                {{ $land->client->name ?? '-' }}
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="mb-1 text-muted small">رقم القطعة</label>
                                        <div>
                                            <span class="badge bg-primary fs-6">{{ $land->land_no }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="mb-1 text-muted small">رقم الوحدة</label>
                                        <div>
                                            @if($land->unit_no)
                                                <span class="badge bg-info fs-6">{{ $land->unit_no }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="mb-1 text-muted small">تاريخ الإضافة</label>
                                        <div class="fw-medium">{{ $land->created_at->format('Y-m-d H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Geographic Location Card --}}
                        <div class="mb-4 border-0 shadow-sm card">
                            <div class="card-header">
                                <h5 class="mb-0 card-title"><i class="ti ti-map-pin me-2"></i>الموقع الجغرافي</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="p-3 text-center border rounded-3 bg-light-subtle">
                                            <i class="ti ti-building fs-2 text-primary"></i>
                                            <div class="mt-2 small text-muted">المحافظة</div>
                                            <div class="fw-medium">{{ $land->governorate->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 text-center border rounded-3 bg-light-subtle">
                                            <i class="ti ti-map-pin fs-2 text-info"></i>
                                            <div class="mt-2 small text-muted">المدينة</div>
                                            <div class="fw-medium">{{ $land->city->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 text-center border rounded-3 bg-light-subtle">
                                            <i class="ti ti-map fs-2 text-success"></i>
                                            <div class="mt-2 small text-muted">الحي</div>
                                            <div class="fw-medium">{{ $land->district->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 text-center border rounded-3 bg-light-subtle">
                                            <i class="ti ti-map-2 fs-2 text-warning"></i>
                                            <div class="mt-2 small text-muted">المنطقة</div>
                                            <div class="fw-medium">{{ $land->zone->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 text-center border rounded-3 bg-light-subtle">
                                            <i class="ti ti-location fs-2 text-danger"></i>
                                            <div class="mt-2 small text-muted">القسم</div>
                                            <div class="fw-medium">{{ $land->area->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                                @if($land->address)
                                <div class="p-3 mt-3 border rounded-3 bg-light-subtle">
                                    <label class="mb-1 text-muted small">العنوان التفصيلي</label>
                                    <div class="fw-medium">{{ $land->address }}</div>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Physical Location Card --}}
                        @if($land->room || $land->lane || $land->stand || $land->rack)
                        <div class="mb-4 border-0 shadow-sm card">
                            <div class="card-header">
                                <h5 class="mb-0 card-title"><i class="ti ti-building-warehouse me-2"></i>موقع التخزين</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <div class="p-3 text-center border rounded-3 bg-primary-subtle">
                                            <i class="ti ti-door fs-2 text-primary"></i>
                                            <div class="mt-2 small text-muted">الغرفة</div>
                                            <div class="fw-medium">{{ $land->room->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 text-center border rounded-3 bg-info-subtle">
                                            <i class="ti ti-arrows-horizontal fs-2 text-info"></i>
                                            <div class="mt-2 small text-muted">الممر</div>
                                            <div class="fw-medium">{{ $land->lane->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 text-center border rounded-3 bg-success-subtle">
                                            <i class="ti ti-layout-board fs-2 text-success"></i>
                                            <div class="mt-2 small text-muted">الحامل</div>
                                            <div class="fw-medium">{{ $land->stand->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 text-center border rounded-3 bg-warning-subtle">
                                            <i class="ti ti-stack-2 fs-2 text-warning"></i>
                                            <div class="mt-2 small text-muted">الرف</div>
                                            <div class="fw-medium">{{ $land->rack->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Files Card --}}
                        <div class="mb-4 border-0 shadow-sm card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 card-title"><i class="ti ti-files me-2"></i>الملفات المرتبطة</h5>
                                <span class="badge bg-primary">{{ $land->files->count() }}</span>
                            </div>
                            <div class="p-0 card-body">
                                @if($land->files->count() > 0)
                                <div class="table-responsive">
                                    <table class="table mb-0 align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>اسم الملف</th>
                                                <th>نوع الملف</th>
                                                <th>تاريخ الإضافة</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($land->files as $file)
                                            <tr>
                                                <td>
                                                    <i class="ti ti-file me-1 text-primary"></i>
                                                    {{ $file->name ?? 'ملف #' . $file->id }}
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $file->type ?? '-' }}</span>
                                                </td>
                                                <td class="text-muted small">{{ $file->created_at->format('Y-m-d') }}</td>
                                                <td>
                                                    <a href="#" class="btn btn-sm bg-info-subtle text-info">
                                                        <i class="ti ti-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="py-4 text-center">
                                    <i class="ti ti-file-off fs-1 text-muted"></i>
                                    <p class="mt-2 text-muted">لا توجد ملفات مرتبطة بهذه الأرض</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Sidebar --}}
                    <div class="col-lg-4">
                        {{-- Quick Actions --}}
                        <div class="mb-4 border-0 shadow-sm card">
                            <div class="card-header">
                                <h5 class="mb-0 card-title"><i class="ti ti-bolt me-2"></i>إجراءات سريعة</h5>
                            </div>
                            <div class="card-body">
                                <div class="gap-2 d-grid">
                                    @can('lands.edit')
                                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editLandModal">
                                        <i class="ti ti-edit me-1"></i>تعديل البيانات
                                    </button>
                                    @endcan
                                    @can('lands.delete')
                                    <form action="{{ route('admin.lands.destroy', $land) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger w-100">
                                            <i class="ti ti-trash me-1"></i>حذف الأرض
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </div>
                        </div>

                        {{-- Notes --}}
                        @if($land->notes)
                        <div class="mb-4 border-0 shadow-sm card">
                            <div class="card-header">
                                <h5 class="mb-0 card-title"><i class="ti ti-notes me-2"></i>ملاحظات</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $land->notes }}</p>
                            </div>
                        </div>
                        @endif

                        {{-- Summary --}}
                        <div class="border-0 shadow-sm card">
                            <div class="card-header">
                                <h5 class="mb-0 card-title"><i class="ti ti-list-details me-2"></i>ملخص</h5>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0 list-unstyled">
                                    <li class="py-2 d-flex justify-content-between border-bottom">
                                        <span class="text-muted">العنوان الكامل</span>
                                    </li>
                                    <li class="py-2">
                                        <small>{{ $land->full_address }}</small>
                                    </li>
                                    <li class="py-2 d-flex justify-content-between border-bottom">
                                        <span class="text-muted">عدد الملفات</span>
                                        <span class="fw-medium">{{ $land->files->count() }}</span>
                                    </li>
                                    <li class="py-2 d-flex justify-content-between border-bottom">
                                        <span class="text-muted">آخر تحديث</span>
                                        <span class="fw-medium">{{ $land->updated_at->diffForHumans() }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    @can('lands.edit')
    <div class="modal fade" id="editLandModal" tabindex="-1">
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
                                    @foreach(\App\Models\Client::orderBy('name')->get() as $client)
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
                                <select name="governorate_id" class="form-select" id="edit_governorate">
                                    <option value="">اختر المحافظة</option>
                                    @foreach(\App\Models\Governorate::all() as $gov)
                                        <option value="{{ $gov->id }}" {{ $land->governorate_id == $gov->id ? 'selected' : '' }}>
                                            {{ $gov->name }}
                                        </option>
                                    @endforeach
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
    @endcan

    @include('admin.main.scripts')
</body>
</html>
