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
                <div class="row justify-content-center" style="min-height: 70vh; align-items: center;">
                    <div class="col-md-5 col-lg-4">
                        <div class="mb-4 text-center">
                            <div class="mb-3" style="width:80px;height:80px;background:#dc354520;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;">
                                <i class="ti ti-database-export text-danger" style="font-size:2.5rem;"></i>
                            </div>
                            <h4 class="mb-1">النسخ الاحتياطي</h4>
                            <p class="mb-0 text-muted">قاعدة البيانات + الملفات المرفوعة</p>
                        </div>

                        <div class="border-0 shadow-sm card">
                            <div class="p-4 card-body" id="backupContent">
                                <div class="p-3 mb-3 rounded d-flex align-items-center bg-light">
                                    <i class="ti ti-database text-success me-3 fs-4"></i>
                                    <span>قاعدة البيانات</span>
                                    <i class="ti ti-check text-success me-auto"></i>
                                </div>
                                <div class="p-3 mb-4 rounded d-flex align-items-center bg-light">
                                    <i class="ti ti-files text-warning me-3 fs-4"></i>
                                    <span>الملفات المرفوعة</span>
                                    <i class="ti ti-check text-success me-auto"></i>
                                </div>

                                <button type="button" class="py-3 btn btn-danger w-100" id="btnBackup" onclick="startBackup()">
                                    <i class="ti ti-download me-2"></i>تحميل النسخة الاحتياطية
                                </button>
                            </div>

                            <div class="p-4 text-center card-body" id="backupProgress" style="display:none;">
                                <div class="mb-3 spinner-border text-danger" style="width:3rem;height:3rem;"></div>
                                <p class="mb-1">جاري إنشاء النسخة...</p>
                                <small class="text-muted">لا تغلق الصفحة</small>
                            </div>
                        </div>

                        <div class="mt-3 text-center">
                            <a href="{{ route('dashboard') }}" class="text-muted small">
                                <i class="ti ti-arrow-right me-1"></i>رجوع
                            </a>
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
                    alert('تم الحفظ بنجاح!');
                } else {
                    window.location.href = '{{ route("admin.backup.download") }}';
                }
            } catch (err) {
                if (err.name !== 'AbortError') {
                    window.location.href = '{{ route("admin.backup.download") }}';
                }
            } finally {
                setTimeout(() => {
                    document.getElementById('backupContent').style.display = 'block';
                    document.getElementById('backupProgress').style.display = 'none';
                }, 1500);
            }
        }
    </script>
</body>
</html>
