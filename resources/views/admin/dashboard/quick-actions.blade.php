{{-- Quick Actions Section --}}
<div class="card">
    <div class="border-dashed card-header">
        <h4 class="mb-0 card-title">
            <i class="ti ti-bolt me-2 text-warning"></i> إجراءات سريعة
        </h4>
    </div>
    <div class="card-body">
        <div class="gap-3 d-grid">
            {{-- Add New Client --}}
            <a href="#" class="btn btn-outline-primary btn-lg d-flex align-items-center justify-content-start">
                <div class="avatar-sm me-3">
                    <div class="rounded avatar-title bg-primary-subtle text-primary">
                        <i class="ti ti-user-plus fs-20"></i>
                    </div>
                </div>
                <div class="text-start">
                    <h6 class="mb-0 fw-semibold">إضافة عميل جديد</h6>
                    <small class="text-muted">تسجيل عميل جديد في النظام</small>
                </div>
            </a>

            {{-- Manage Physical Locations --}}
            <a href="#" class="btn btn-outline-success btn-lg d-flex align-items-center justify-content-start">
                <div class="avatar-sm me-3">
                    <div class="rounded avatar-title bg-success-subtle text-success">
                        <i class="ti ti-building-warehouse fs-20"></i>
                    </div>
                </div>
                <div class="text-start">
                    <h6 class="mb-0 fw-semibold">إدارة مواقع التخزين</h6>
                    <small class="text-muted">الغرف والممرات والأرفف</small>
                </div>
            </a>

            {{-- Manage Geographic Areas --}}
            <a href="#" class="btn btn-outline-info btn-lg d-flex align-items-center justify-content-start">
                <div class="avatar-sm me-3">
                    <div class="rounded avatar-title bg-info-subtle text-info">
                        <i class="ti ti-map-pin fs-20"></i>
                    </div>
                </div>
                <div class="text-start">
                    <h6 class="mb-0 fw-semibold">إدارة المناطق الجغرافية</h6>
                    <small class="text-muted">المحافظات والمدن والأحياء</small>
                </div>
            </a>

            {{-- Backup System --}}
            <button type="button" onclick="quickBackup()" class="btn btn-outline-danger btn-lg d-flex align-items-center justify-content-start" id="quickBackupBtn">
                <div class="avatar-sm me-3">
                    <div class="rounded avatar-title bg-danger-subtle text-danger">
                        <i class="ti ti-database-export fs-20"></i>
                    </div>
                </div>
                <div class="text-start">
                    <h6 class="mb-0 fw-semibold">نسخة احتياطية</h6>
                    <small class="text-muted">قاعدة البيانات + الملفات</small>
                </div>
            </button>
        </div>
    </div>
</div>

{{-- Backup Progress Modal --}}
<div class="modal fade" id="quickBackupModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="p-4 text-center modal-content">
            <div class="mx-auto mb-3 spinner-border text-danger" style="width:3rem;height:3rem;"></div>
            <p class="mb-1">جاري إنشاء النسخة...</p>
            <small class="text-muted">لا تغلق الصفحة</small>
        </div>
    </div>
</div>

<script>
async function quickBackup() {
    const btn = document.getElementById('quickBackupBtn');
    const modal = new bootstrap.Modal(document.getElementById('quickBackupModal'));

    btn.disabled = true;
    modal.show();

    try {
        if ('showSaveFilePicker' in window) {
            const handle = await window.showSaveFilePicker({
                suggestedName: 'backup_' + new Date().toISOString().slice(0,10) + '.zip',
                types: [{ accept: { 'application/zip': ['.zip'] } }]
            });
            const response = await fetch('{{ route("admin.backup.download") }}');
            if (!response.ok) throw new Error('Backup failed');
            const blob = await response.blob();
            const writable = await handle.createWritable();
            await writable.write(blob);
            await writable.close();
            modal.hide();
            alert('تم الحفظ بنجاح!');
        } else {
            window.location.href = '{{ route("admin.backup.download") }}';
        }
    } catch (err) {
        modal.hide();
        if (err.name !== 'AbortError') {
            window.location.href = '{{ route("admin.backup.download") }}';
        }
    } finally {
        btn.disabled = false;
    }
}
</script>
