<!DOCTYPE html>
@include('admin.main.html')

<head>
    <title>النسخ الاحتياطي - أرشيف القاهرة الجديدة</title>
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
                        <div class="mt-3 mb-4 page-title-box">
                            <div class="px-4 py-3 border border-opacity-10 shadow-sm d-flex flex-column flex-lg-row align-items-lg-center justify-content-between bg-body border-secondary rounded-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-md me-3">
                                        <span class="avatar-title bg-danger-subtle text-danger rounded-circle">
                                            <i class="ti ti-database-export fs-22"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h4 class="mb-1 page-title">النسخ الاحتياطي</h4>
                                        <nav aria-label="breadcrumb">
                                            <ol class="p-0 mb-0 bg-transparent breadcrumb">
                                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                                                <li class="breadcrumb-item active">النسخ الاحتياطي</li>
                                            </ol>
                                        </nav>
                                    </div>
                                </div>
                                <div class="mt-3 mt-lg-0">
                                    <span class="px-3 py-2 badge bg-danger-subtle text-danger fs-13">
                                        <i class="ti ti-shield-check me-1"></i>
                                        حماية البيانات
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Backup Action Card -->
                    <div class="mb-4 col-lg-4 col-md-5">
                        <div class="border-0 shadow-sm card h-100">
                            <div class="p-4 card-body">
                                <!-- Header -->
                                <div class="mb-4 text-center">
                                    <div class="mb-3 position-relative d-inline-block">
                                        <div class="mx-auto avatar-lg">
                                            <span class="avatar-title bg-danger-subtle text-danger rounded-circle" style="width:70px;height:70px;">
                                                <i class="ti ti-database-export" style="font-size:2rem;"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <h5 class="mt-3 mb-1">إنشاء نسخة احتياطية</h5>
                                    <p class="mb-0 text-muted small">نسخة كاملة من النظام</p>
                                </div>
                                <!-- Content -->
                                <div id="backupContent">
                                    <!-- Backup Items -->
                                    <div class="mb-4 border rounded-3">
                                        <div class="p-3 d-flex align-items-center border-bottom">
                                            <div class="avatar-sm me-3">
                                                <span class="rounded avatar-title bg-success-subtle text-success">
                                                    <i class="ti ti-database fs-18"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 fs-14">قاعدة البيانات</h6>
                                                <small class="text-muted">جميع الجداول والبيانات</small>
                                            </div>
                                            <i class="ti ti-circle-check text-success fs-20"></i>
                                        </div>
                                        <div class="p-3 d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <span class="rounded avatar-title bg-warning-subtle text-warning">
                                                    <i class="ti ti-files fs-18"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 fs-14">الملفات المرفوعة</h6>
                                                <small class="text-muted">الصور والمستندات</small>
                                            </div>
                                            <i class="ti ti-circle-check text-success fs-20"></i>
                                        </div>
                                    </div>

                                    <!-- Download Button -->
                                    <button type="button" class="py-2 btn btn-danger w-100" id="btnBackup" onclick="startBackup()">
                                        <i class="ti ti-download me-2"></i>تحميل النسخة الاحتياطية
                                    </button>

                                    <!-- Info -->
                                    <div class="mt-3 text-center">
                                        <small class="text-muted">
                                            <i class="ti ti-info-circle me-1"></i>
                                            سيتم تحميل ملف ZIP يحتوي على النسخة الكاملة
                                        </small>
                                    </div>
                                </div>

                                <!-- Progress -->
                                <div id="backupProgress" style="display:none;">
                                    <div class="py-4 text-center">
                                        <div class="mb-3 position-relative d-inline-block">
                                            <div class="spinner-border text-danger" style="width:3.5rem;height:3.5rem;"></div>
                                            <div class="position-absolute top-50 start-50 translate-middle">
                                                <i class="ti ti-database-export text-danger"></i>
                                            </div>
                                        </div>
                                        <h6 class="mb-1">جاري إنشاء النسخة الاحتياطية...</h6>
                                        <p class="mb-3 text-muted small">يرجى الانتظار وعدم إغلاق الصفحة</p>
                                        <div class="progress" style="height:6px;">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" style="width:100%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="py-2 text-center card-footer bg-light-subtle border-top">
                                <a href="{{ route('dashboard') }}" class="text-muted small text-decoration-none">
                                    <i class="ti ti-arrow-right me-1"></i>العودة للوحة التحكم
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Backup History -->
                    <div class="col-lg-8 col-md-7">
                        <div class="border-0 shadow-sm card">
                            <div class="py-3 bg-white card-header d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">
                                    <i class="ti ti-history me-2 text-primary"></i>سجل النسخ الاحتياطية
                                </h5>
                                @if($backups->count() > 0)
                                <span class="px-3 badge bg-primary-subtle text-primary rounded-pill">{{ $backups->total() }} نسخة</span>
                                @endif
                            </div>
                            <div class="p-0 card-body">
                                @if($backups->count() > 0)
                                <div class="table-responsive">
                                    <table class="table mb-0 align-middle table-hover">
                                        <thead>
                                            <tr class="bg-light-subtle">
                                                <th class="py-3 ps-4" style="min-width:220px;">الملف</th>
                                                <th class="py-3 text-center">الحجم</th>
                                                <th class="py-3 text-center">الحالة</th>
                                                <th class="py-3 text-center">بواسطة</th>
                                                <th class="py-3 text-center pe-4">التاريخ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($backups as $backup)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar-sm">
                                                                <span class="rounded avatar-title bg-warning-subtle text-warning">
                                                                    <i class="ti ti-file-zip fs-18"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-0 fs-14">{{ $backup->file_name }}</h6>
                                                            <small class="text-muted">{{ $backup->type_arabic }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="fw-medium">{{ $backup->file_size_formatted }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @if($backup->status === 'completed')
                                                        <span class="px-3 badge bg-success-subtle text-success rounded-pill">
                                                            <i class="ti ti-check me-1"></i>{{ $backup->status_arabic }}
                                                        </span>
                                                    @elseif($backup->status === 'pending')
                                                        <span class="px-3 badge bg-warning-subtle text-warning rounded-pill">
                                                            <i class="ti ti-clock me-1"></i>{{ $backup->status_arabic }}
                                                        </span>
                                                    @else
                                                        <span class="px-3 badge bg-danger-subtle text-danger rounded-pill">
                                                            <i class="ti ti-x me-1"></i>{{ $backup->status_arabic }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <div class="avatar-xs me-2">
                                                            <span class="avatar-title bg-secondary-subtle text-secondary rounded-circle">
                                                                <i class="ti ti-user fs-12"></i>
                                                            </span>
                                                        </div>
                                                        <span class="text-muted">{{ $backup->creator?->name ?? '-' }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-center pe-4">
                                                    <div>
                                                        <span class="d-block fw-medium">{{ $backup->created_at->format('Y-m-d') }}</span>
                                                        <small class="text-muted">{{ $backup->created_at->format('H:i') }}</small>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-3 border-top">
                                    {{ $backups->links() }}
                                </div>
                                @else
                                <div class="p-5 text-center">
                                    <div class="mb-3" style="width:80px;height:80px;background:#6c757d15;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;">
                                        <i class="ti ti-database-off text-muted" style="font-size:2rem;"></i>
                                    </div>
                                    <h6 class="mb-1 text-muted">لا توجد نسخ احتياطية</h6>
                                    <p class="mb-0 text-muted small">قم بإنشاء أول نسخة احتياطية من القائمة الجانبية</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.main.scripts')

    <script>
        async function startBackup() {
            document.getElementById('backupContent').style.display = 'none';
            document.getElementById('backupProgress').style.display = 'block';

            try {
                if ('showSaveFilePicker' in window) {
                    const handle = await window.showSaveFilePicker({
                        suggestedName: 'backup_' + new Date().toISOString().slice(0,10) + '.zip',
                        types: [{ accept: { 'application/zip': ['.zip'] } }]
                    });
                    const response = await fetch('{{ route("admin.backup.download") }}');
                    const blob = await response.blob();
                    const writable = await handle.createWritable();
                    await writable.write(blob);
                    await writable.close();
                    // Refresh page after successful backup
                    window.location.reload();
                } else {
                    // For browsers without File System API, use iframe to download and then refresh
                    const iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    iframe.src = '{{ route("admin.backup.download") }}';
                    document.body.appendChild(iframe);
                    // Refresh after download starts
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                }
            } catch (err) {
                if (err.name !== 'AbortError') {
                    const iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    iframe.src = '{{ route("admin.backup.download") }}';
                    document.body.appendChild(iframe);
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                } else {
                    // User cancelled - restore UI
                    document.getElementById('backupContent').style.display = 'block';
                    document.getElementById('backupProgress').style.display = 'none';
                }
            }
        }
    </script>
</body>
</html>
