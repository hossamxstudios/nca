<!DOCTYPE html>
@include('admin.main.html')

<head>
    <title>أنواع المحتوى - أرشيف القاهرة الجديدة</title>
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
                                        <i class="ti ti-tags me-1"></i> أنواع المحتوى
                                    </span>
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb mb-0 mt-1">
                                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                                            <li class="breadcrumb-item active">أنواع المحتوى</li>
                                        </ol>
                                    </nav>
                                </div>
                                <div class="d-flex gap-2 mt-2 mt-lg-0">
                                    @can('items.create')
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                        <i class="ti ti-plus me-1"></i> إضافة نوع محتوى
                                    </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stats Cards --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md bg-primary-subtle text-primary rounded">
                                        <i class="ti ti-tags fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h4 class="mb-0">{{ $stats['total'] }}</h4>
                                        <small class="text-muted">إجمالي الأنواع</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md bg-success-subtle text-success rounded">
                                        <i class="ti ti-file-check fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h4 class="mb-0">{{ $stats['with_files'] }}</h4>
                                        <small class="text-muted">مرتبط بملفات</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md bg-warning-subtle text-warning rounded">
                                        <i class="ti ti-file-off fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h4 class="mb-0">{{ $stats['without_files'] }}</h4>
                                        <small class="text-muted">غير مستخدم</small>
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
                                    <h5 class="card-title mb-0">قائمة أنواع المحتوى</h5>
                                    <span class="badge bg-primary-subtle text-primary">{{ count($items) }} نوع</span>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="60">الترتيب</th>
                                                <th>الاسم</th>
                                                <th>الوصف</th>
                                                <th>عدد الملفات</th>
                                                <th width="150" class="text-center">الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemsTableBody">
                                            @forelse($items as $item)
                                            <tr data-id="{{ $item->id }}">
                                                <td>
                                                    <span class="badge bg-secondary">{{ $item->order }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm bg-primary-subtle text-primary rounded me-2 d-flex align-items-center justify-content-center">
                                                            <i class="ti ti-tag"></i>
                                                        </div>
                                                        <span class="fw-medium">{{ $item->name }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ $item->description ?: '-' }}</span>
                                                </td>
                                                <td>
                                                    @if($item->files_count > 0)
                                                    <span class="badge bg-success">{{ $item->files_count }} ملف</span>
                                                    @else
                                                    <span class="badge bg-secondary">0</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        @can('items.edit')
                                                        <button class="btn btn-soft-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editItemModal_{{ $item->id }}" title="تعديل">
                                                            <i class="ti ti-edit"></i>
                                                        </button>
                                                        @endcan
                                                        @can('items.delete')
                                                        @if($item->files_count == 0)
                                                        <form action="{{ route('admin.items.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا النوع؟')">
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
                                                <td colspan="5" class="text-center py-5">
                                                    <div class="text-muted">
                                                        <i class="ti ti-tags-off fs-1 d-block mb-2"></i>
                                                        <p class="mb-2">لا توجد أنواع محتوى</p>
                                                        @can('items.create')
                                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                                            <i class="ti ti-plus me-1"></i>إضافة نوع محتوى
                                                        </button>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Item Modal --}}
    <div class="modal fade" id="addItemModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.items.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-tag me-2 text-primary"></i>إضافة نوع محتوى جديد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">الاسم <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="مثال: عقود">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="وصف اختياري لنوع المحتوى"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الترتيب</label>
                            <input type="number" name="order" class="form-control" value="0" min="0" placeholder="0">
                            <small class="text-muted">رقم أقل = يظهر أولاً</small>
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

    {{-- Edit Item Modals (one per item) --}}
    @foreach($items as $item)
    <div class="modal fade" id="editItemModal_{{ $item->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.items.update', $item) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-edit me-2 text-warning"></i>تعديل نوع المحتوى</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">الاسم <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="{{ $item->name }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea name="description" class="form-control" rows="2">{{ $item->description }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الترتيب</label>
                            <input type="number" name="order" class="form-control" value="{{ $item->order }}" min="0">
                            <small class="text-muted">رقم أقل = يظهر أولاً</small>
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
    @endforeach

    @include('admin.main.scripts')
</body>
</html>
