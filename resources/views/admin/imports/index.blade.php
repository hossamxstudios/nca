<!DOCTYPE html>
@include('admin.main.html')
<head>
    <title>استيراد البيانات - أرشيف القاهرة الجديدة</title>
    @include('admin.main.meta')
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
                        <div class="page-title-box">
                            <div class="page-title-right">
                                <ol class="m-0 breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                                    <li class="breadcrumb-item active">استيراد البيانات</li>
                                </ol>
                            </div>
                            <h4 class="page-title">استيراد البيانات</h4>
                        </div>
                    </div>
                </div>

                {{-- Alerts --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ti ti-check me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ti ti-x me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Stats Cards --}}
                <div class="row">
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="ti ti-file-import widget-icon bg-primary-lighten text-primary"></i>
                                </div>
                                <h5 class="mt-0 text-muted fw-normal">إجمالي الاستيرادات</h5>
                                <h3 class="mt-3 mb-0">{{ number_format($totalImports) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="ti ti-clock widget-icon bg-secondary-lighten text-secondary"></i>
                                </div>
                                <h5 class="mt-0 text-muted fw-normal">قيد الانتظار</h5>
                                <h3 class="mt-3 mb-0">{{ number_format($pendingImports) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="ti ti-loader widget-icon bg-warning-lighten text-warning"></i>
                                </div>
                                <h5 class="mt-0 text-muted fw-normal">قيد المعالجة</h5>
                                <h3 class="mt-3 mb-0">{{ number_format($processingImports) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="ti ti-check widget-icon bg-success-lighten text-success"></i>
                                </div>
                                <h5 class="mt-0 text-muted fw-normal">مكتملة</h5>
                                <h3 class="mt-3 mb-0">{{ number_format($completedImports) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-end">
                                    <i class="ti ti-x widget-icon bg-danger-lighten text-danger"></i>
                                </div>
                                <h5 class="mt-0 text-muted fw-normal">فاشلة</h5>
                                <h3 class="mt-3 mb-0">{{ number_format($failedImports) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Main Card --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="mb-0 header-title">
                                    <i class="ti ti-file-import me-2"></i>سجل الاستيرادات
                                </h4>
                                <div class="gap-2 d-flex">
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="ti ti-download me-1"></i>تحميل قالب
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('admin.imports.download-template', ['type' => 'archive']) }}"><i class="ti ti-archive me-2"></i>قالب الأرشيف</a></li>
                                            <li><a class="dropdown-item" href="{{ route('admin.imports.download-template', ['type' => 'full']) }}"><i class="ti ti-file me-2"></i>قالب كامل</a></li>
                                            <li><a class="dropdown-item" href="{{ route('admin.imports.download-template', ['type' => 'clients']) }}"><i class="ti ti-users me-2"></i>قالب العملاء</a></li>
                                            <li><a class="dropdown-item" href="{{ route('admin.imports.download-template', ['type' => 'lands']) }}"><i class="ti ti-map me-2"></i>قالب القطع</a></li>
                                            <li><a class="dropdown-item" href="{{ route('admin.imports.download-template', ['type' => 'geographic']) }}"><i class="ti ti-map-pin me-2"></i>قالب المناطق الجغرافية</a></li>
                                        </ul>
                                    </div>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                        <i class="ti ti-upload me-1"></i>رفع ملف جديد
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                @if($imports->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table mb-0 table-hover table-centered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>اسم الملف</th>
                                                    <th>النوع</th>
                                                    <th>الحالة</th>
                                                    <th>التقدم</th>
                                                    <th>الصفوف</th>
                                                    <th>المستخدم</th>
                                                    <th>التاريخ</th>
                                                    <th class="text-center">الإجراءات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($imports as $import)
                                                    <tr id="import-row-{{ $import->id }}">
                                                        <td>{{ $import->id }}</td>
                                                        <td>
                                                            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $import->original_filename }}">
                                                                <i class="ti ti-file-spreadsheet text-success me-1"></i>
                                                                {{ $import->original_filename }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info-lighten text-info">{{ $import->type_label }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="badge {{ $import->status_badge['class'] }}" id="status-badge-{{ $import->id }}">
                                                                <i class="ti {{ $import->status_badge['icon'] }} me-1"></i>
                                                                {{ $import->status_badge['text'] }}
                                                            </span>
                                                        </td>
                                                        <td style="min-width: 120px;">
                                                            <div class="progress progress-sm" style="height: 8px;">
                                                                <div class="progress-bar bg-success" id="progress-bar-{{ $import->id }}"
                                                                     role="progressbar"
                                                                     style="width: {{ $import->progress_percentage }}%"
                                                                     aria-valuenow="{{ $import->progress_percentage }}"
                                                                     aria-valuemin="0"
                                                                     aria-valuemax="100">
                                                                </div>
                                                            </div>
                                                            <small class="text-muted" id="progress-text-{{ $import->id }}">{{ $import->progress_percentage }}%</small>
                                                        </td>
                                                        <td>
                                                            <span id="rows-info-{{ $import->id }}">
                                                                <span class="text-success">{{ $import->success_rows }}</span> /
                                                                <span class="text-danger">{{ $import->failed_rows }}</span> /
                                                                <span class="text-muted">{{ $import->total_rows }}</span>
                                                            </span>
                                                        </td>
                                                        <td>{{ $import->user?->name ?? '-' }}</td>
                                                        <td>
                                                            <small>{{ $import->created_at->format('Y-m-d H:i') }}</small>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="btn-group">
                                                                <a href="{{ route('admin.imports.show', $import) }}"
                                                                   class="btn btn-sm btn-soft-info"
                                                                   title="عرض التفاصيل">
                                                                    <i class="ti ti-eye"></i>
                                                                </a>
                                                                @if(in_array($import->status, ['completed', 'failed']))
                                                                    <button type="button" class="btn btn-sm btn-soft-danger"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#deleteModal_{{ $import->id }}"
                                                                            title="حذف">
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

                                    {{-- Pagination --}}
                                    <div class="mt-3 d-flex justify-content-center">
                                        {{ $imports->links() }}
                                    </div>
                                @else
                                    <div class="py-5 text-center">
                                        <i class="ti ti-file-off display-4 text-muted"></i>
                                        <p class="mt-3 mb-0 text-muted">لا توجد عمليات استيراد بعد</p>
                                        <button type="button" class="mt-3 btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                            <i class="ti ti-upload me-1"></i>رفع ملف جديد
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Upload Modal --}}
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.imports.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ti ti-upload me-2"></i>رفع ملف استيراد جديد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">نوع الاستيراد <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" required id="importType">
                                    <option value="">اختر نوع الاستيراد</option>
                                    <option value="archive">استيراد الأرشيف</option>
                                    <option value="full">استيراد كامل</option>
                                    <option value="clients">عملاء فقط</option>
                                    <option value="lands">قطع فقط</option>
                                    <option value="geographic">مناطق جغرافية</option>
                                </select>
                                <div class="form-text" id="typeDescription"></div>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">ملف Excel <span class="text-danger">*</span></label>
                                <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                                <div class="form-text">الصيغ المدعومة: xlsx, xls, csv (الحد الأقصى: 50 ميجابايت)</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="skip_errors" id="skipErrors" value="1" checked>
                                    <label class="form-check-label" for="skipErrors">تخطي الصفوف التي بها أخطاء</label>
                                </div>
                                <div class="form-text">في حالة التفعيل، سيتم تخطي الصفوف التي بها أخطاء والاستمرار في الاستيراد</div>
                            </div>
                            <div class="mb-3 col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="update_existing" id="updateExisting" value="1">
                                    <label class="form-check-label" for="updateExisting">تحديث السجلات الموجودة</label>
                                </div>
                                <div class="form-text">في حالة التفعيل، سيتم تحديث السجلات الموجودة بدلاً من إنشاء سجلات جديدة</div>
                            </div>
                        </div>

                        {{-- Type-specific instructions --}}
                        <div class="mb-0 alert alert-info" id="importInstructions" style="display: none;">
                            <h6 class="alert-heading"><i class="ti ti-info-circle me-1"></i>تعليمات الاستيراد</h6>
                            <div id="instructionsContent"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="ti ti-upload me-1"></i>رفع وبدء الاستيراد
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Details Modal --}}
    <div class="modal fade" id="detailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ti ti-info-circle me-2"></i>تفاصيل الاستيراد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailsContent">
                    <div class="py-4 text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modals --}}
    @foreach($imports as $import)
        @if(in_array($import->status, ['completed', 'failed']))
            <div class="modal fade" id="deleteModal_{{ $import->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('admin.imports.destroy', $import) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title text-danger"><i class="ti ti-trash me-2"></i>حذف سجل الاستيراد</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>هل أنت متأكد من حذف سجل الاستيراد:</p>
                                <p class="fw-bold">{{ $import->original_filename }}</p>
                                <div class="mb-0 alert alert-warning">
                                    <i class="ti ti-alert-triangle me-1"></i>
                                    هذا الإجراء سيحذف سجل الاستيراد فقط ولن يؤثر على البيانات المستوردة.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                <button type="submit" class="btn btn-danger">
                                    <i class="ti ti-trash me-1"></i>حذف
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    @include('admin.main.scripts')

    <script>
        // Type descriptions and instructions
        const typeInfo = {
            archive: {
                description: 'استيراد ملفات الأرشيف مع العملاء والقطع والمواقع',
                instructions: `
                    <p>يجب أن يحتوي الملف على الأعمدة التالية:</p>
                    <ul class="mb-0">
                        <li><strong>رقم:</strong> رقم الصف في Excel</li>
                        <li><strong>الملف:</strong> اسم الملف (إذا كان فارغ أو "لا يوجد" سيتم تجاهله)</li>
                        <li><strong>المالك:</strong> اسم العميل (إذا كان فارغ سيتم إضافته كـ "لا يوجد اسم")</li>
                        <li><strong>القطعه:</strong> رقم القطعة</li>
                        <li><strong>الحي:</strong> اسم الحي</li>
                        <li><strong>المنطقة:</strong> اسم المنطقة</li>
                        <li><strong>المجاورة:</strong> اسم المجاورة</li>
                        <li><strong>الاوضة:</strong> اسم الغرفة</li>
                        <li><strong>الممر:</strong> اسم الممر</li>
                        <li><strong>الاستند:</strong> اسم الستاند</li>
                        <li><strong>الرف:</strong> اسم الرف</li>
                    </ul>
                `
            },
            full: {
                description: 'استيراد كامل للعملاء مع القطع والمواقع الجغرافية',
                instructions: `
                    <p>يجب أن يحتوي الملف على بيانات العملاء والقطع والمواقع الجغرافية.</p>
                `
            },
            clients: {
                description: 'استيراد بيانات العملاء فقط',
                instructions: `
                    <p>يجب أن يحتوي الملف على بيانات العملاء (الاسم، الرقم القومي، الهاتف، الموبايل).</p>
                `
            },
            lands: {
                description: 'استيراد بيانات القطع فقط',
                instructions: `
                    <p>يجب أن يحتوي الملف على بيانات القطع مع ربطها بالعملاء والمواقع الجغرافية.</p>
                `
            },
            geographic: {
                description: 'استيراد المناطق الجغرافية فقط',
                instructions: `
                    <p>يجب أن يحتوي الملف على تسلسل المناطق الجغرافية (المحافظة، المدينة، الحي، المنطقة، المجاورة).</p>
                `
            }
        };

        // Handle type change
        document.getElementById('importType').addEventListener('change', function() {
            const type = this.value;
            const descEl = document.getElementById('typeDescription');
            const instructionsEl = document.getElementById('importInstructions');
            const contentEl = document.getElementById('instructionsContent');

            if (type && typeInfo[type]) {
                descEl.textContent = typeInfo[type].description;
                contentEl.innerHTML = typeInfo[type].instructions;
                instructionsEl.style.display = 'block';
            } else {
                descEl.textContent = '';
                instructionsEl.style.display = 'none';
            }
        });

        // Show import details
        function showImportDetails(importId) {
            const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
            const contentEl = document.getElementById('detailsContent');

            contentEl.innerHTML = `
                <div class="py-4 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                </div>
            `;

            modal.show();

            fetch(`{{ url('imports') }}/${importId}/show`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const imp = data.import;
                        contentEl.innerHTML = `
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label text-muted">اسم الملف</label>
                                    <p class="mb-0 fw-bold">${imp.original_filename}</p>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label text-muted">النوع</label>
                                    <p class="mb-0"><span class="badge bg-info">${imp.type_label}</span></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label text-muted">الحالة</label>
                                    <p class="mb-0"><span class="badge ${imp.status_badge.class}"><i class="ti ${imp.status_badge.icon} me-1"></i>${imp.status_badge.text}</span></p>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label text-muted">التقدم</label>
                                    <div class="mb-1 progress" style="height: 10px;">
                                        <div class="progress-bar bg-success" style="width: ${imp.progress_percentage}%"></div>
                                    </div>
                                    <small class="text-muted">${imp.progress_percentage}%</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-md-3">
                                    <label class="form-label text-muted">إجمالي الصفوف</label>
                                    <p class="mb-0 fw-bold">${imp.total_rows.toLocaleString()}</p>
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label text-muted">تمت معالجتها</label>
                                    <p class="mb-0 fw-bold">${imp.processed_rows.toLocaleString()}</p>
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label text-muted">ناجحة</label>
                                    <p class="mb-0 fw-bold text-success">${imp.success_rows.toLocaleString()}</p>
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label text-muted">فاشلة</label>
                                    <p class="mb-0 fw-bold text-danger">${imp.failed_rows.toLocaleString()}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label class="form-label text-muted">المستخدم</label>
                                    <p class="mb-0">${imp.user || '-'}</p>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label class="form-label text-muted">وقت البدء</label>
                                    <p class="mb-0">${imp.started_at || '-'}</p>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label class="form-label text-muted">وقت الانتهاء</label>
                                    <p class="mb-0">${imp.completed_at || '-'}</p>
                                </div>
                            </div>
                            ${imp.errors && Object.keys(imp.errors).length > 0 ? `
                                <div class="alert alert-danger">
                                    <h6 class="alert-heading"><i class="ti ti-alert-triangle me-1"></i>الأخطاء</h6>
                                    <div style="max-height: 200px; overflow-y: auto;">
                                        <pre class="mb-0" style="white-space: pre-wrap; font-size: 12px;">${JSON.stringify(imp.errors, null, 2)}</pre>
                                    </div>
                                </div>
                            ` : ''}
                            ${imp.summary ? `
                                <div class="mb-0 alert alert-info">
                                    <h6 class="alert-heading"><i class="ti ti-info-circle me-1"></i>الملخص</h6>
                                    <ul class="mb-0">
                                        <li>إجمالي: ${imp.summary.total?.toLocaleString() || 0}</li>
                                        <li>ناجح: ${imp.summary.success?.toLocaleString() || 0}</li>
                                        <li>فاشل: ${imp.summary.failed?.toLocaleString() || 0}</li>
                                    </ul>
                                </div>
                            ` : ''}
                        `;
                    }
                })
                .catch(error => {
                    contentEl.innerHTML = `
                        <div class="mb-0 alert alert-danger">
                            <i class="ti ti-alert-triangle me-1"></i>
                            فشل في تحميل التفاصيل
                        </div>
                    `;
                });
        }

        // Auto-refresh for processing imports
        function refreshProcessingImports() {
            const processingRows = document.querySelectorAll('[id^="import-row-"]');
            processingRows.forEach(row => {
                const importId = row.id.replace('import-row-', '');
                const statusBadge = document.getElementById(`status-badge-${importId}`);

                if (statusBadge && (statusBadge.textContent.includes('جاري') || statusBadge.textContent.includes('انتظار'))) {
                    fetch(`{{ url('imports') }}/${importId}/progress`)
                        .then(response => response.json())
                        .then(data => {
                            // Update progress bar
                            const progressBar = document.getElementById(`progress-bar-${importId}`);
                            const progressText = document.getElementById(`progress-text-${importId}`);
                            const rowsInfo = document.getElementById(`rows-info-${importId}`);

                            if (progressBar) {
                                progressBar.style.width = `${data.progress_percentage}%`;
                            }
                            if (progressText) {
                                progressText.textContent = `${data.progress_percentage}%`;
                            }
                            if (rowsInfo) {
                                rowsInfo.innerHTML = `
                                    <span class="text-success">${data.success_rows}</span> /
                                    <span class="text-danger">${data.failed_rows}</span> /
                                    <span class="text-muted">${data.total_rows}</span>
                                `;
                            }

                            // Update status badge
                            if (statusBadge && data.status_badge) {
                                statusBadge.className = `badge ${data.status_badge.class}`;
                                statusBadge.innerHTML = `<i class="ti ${data.status_badge.icon} me-1"></i>${data.status_badge.text}`;
                            }

                            // If completed or failed, stop polling and reload
                            if (data.status === 'completed' || data.status === 'failed') {
                                setTimeout(() => location.reload(), 1000);
                            }
                        });
                }
            });
        }

        // Poll every 3 seconds
        setInterval(refreshProcessingImports, 3000);

        // Form submission loading state
        document.getElementById('uploadForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>جاري الرفع...';
        });
    </script>
</body>
</html>
