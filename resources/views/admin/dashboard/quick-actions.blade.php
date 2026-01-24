{{-- Quick Actions Section --}}
<div class="card">
    <div class="card-header border-dashed">
        <h4 class="mb-0 card-title">
            <i class="ti ti-bolt me-2 text-warning"></i> إجراءات سريعة
        </h4>
    </div>
    <div class="card-body">
        <div class="gap-3 d-grid">
            {{-- Add New Client --}}
            <a href="#" class="btn btn-outline-primary btn-lg d-flex align-items-center justify-content-start">
                <div class="avatar-sm me-3">
                    <div class="avatar-title bg-primary-subtle text-primary rounded">
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
                    <div class="avatar-title bg-success-subtle text-success rounded">
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
                    <div class="avatar-title bg-info-subtle text-info rounded">
                        <i class="ti ti-map-pin fs-20"></i>
                    </div>
                </div>
                <div class="text-start">
                    <h6 class="mb-0 fw-semibold">إدارة المناطق الجغرافية</h6>
                    <small class="text-muted">المحافظات والمدن والأحياء</small>
                </div>
            </a>

            {{-- Backup System - Direct Download --}}
            @can('backup.create')
            <a href="{{ route('admin.backup.download') }}" class="btn btn-outline-danger btn-lg d-flex align-items-center justify-content-start" onclick="this.innerHTML='<div class=\'spinner-border spinner-border-sm text-danger me-2\'></div> جاري إنشاء النسخة...'">
                <div class="avatar-sm me-3">
                    <div class="avatar-title bg-danger-subtle text-danger rounded">
                        <i class="ti ti-database-export fs-20"></i>
                    </div>
                </div>
                <div class="text-start">
                    <h6 class="mb-0 fw-semibold">تحميل نسخة احتياطية</h6>
                    <small class="text-muted">تنزيل مباشر للنسخة الاحتياطية</small>
                </div>
            </a>
            @endcan
        </div>
    </div>
</div>
