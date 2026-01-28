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
                        <div class="mt-3 mb-2 page-title-box">
                            <div class="px-3 py-2 border border-opacity-10 shadow-sm d-flex flex-column flex-lg-row align-items-lg-center justify-content-between bg-body border-secondary rounded-3">
                                <div>
                                    <span class="px-2 shadow-sm badge bg-primary-subtle text-primary fw-normal d-inline-flex align-items-center">
                                        <i class="ti ti-tags me-1"></i> أنواع المحتوى
                                    </span>
                                    <nav aria-label="breadcrumb">
                                        <ol class="mt-1 mb-0 breadcrumb">
                                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                                            <li class="breadcrumb-item active">أنواع المحتوى</li>
                                        </ol>
                                    </nav>
                                </div>
                                <div class="gap-2 mt-2 d-flex mt-lg-0">
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
                <div class="mb-4 row g-3">
                    <div class="col-md-4">
                        <div class="border-0 shadow-sm card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="rounded avatar avatar-md bg-primary-subtle text-primary">
                                        <i class="ti ti-tags fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h4 class="mb-0">{{ $stats['total'] }}</h4>
                                        <span class="text-dark">إجمالي الأنواع</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border-0 shadow-sm card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="rounded avatar avatar-md bg-success-subtle text-success">
                                        <i class="ti ti-file-check fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h4 class="mb-0">{{ $stats['with_files'] }}</h4>
                                        <span class="text-dark">مرتبط بملفات</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border-0 shadow-sm card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="rounded avatar avatar-md bg-warning-subtle text-warning">
                                        <i class="ti ti-file-off fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h4 class="mb-0">{{ $stats['without_files'] }}</h4>
                                        <span class="text-dark">غير مستخدم</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Data Table --}}
                <div class="row">
                    <div class="col-12">
                        <div class="border-0 shadow-sm card rounded-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="gap-3 d-flex align-items-center">
                                    <h5 class="mb-0 card-title">قائمة أنواع المحتوى</h5>
                                    <span class="badge bg-primary-subtle text-primary">{{ count($items) }} نوع</span>
                                </div>
                            </div>
                            <div class="p-0 card-body">
                                <div class="table-responsive">
                                    <table class="table mb-0 table-hover">
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
                                                        <div class="rounded avatar avatar-sm bg-primary-subtle text-primary me-2 d-flex align-items-center justify-content-center">
                                                            <i class="ti ti-tag"></i>
                                                        </div>
                                                        <span class="fw-medium">{{ $item->name }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-dark">{{ $item->description ?: '-' }}</span>
                                                </td>
                                                <td>
                                                    @if($item->files_count > 0)
                                                    <span class="badge bg-primary fs-5">{{ $item->files_count }} ملف</span>
                                                    @else
                                                    <span class="badge bg-primary fs-5">0</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="gap-1 d-flex justify-content-center">
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
                                                <td colspan="5" class="py-5 text-center">
                                                    <div class="text-muted">
                                                        <i class="mb-2 ti ti-tags-off fs-1 d-block"></i>
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
