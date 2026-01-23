{{-- Stats Cards Section --}}
<div class="mb-4 row row-cols-xxl-3 row-cols-md-3 row-cols-1">
    {{-- Total Clients Card --}}
    <div class="col">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="mb-3 text-uppercase text-muted">إجمالي العملاء</h5>
                        <h2 class="mb-0 fw-bold text-primary">{{ number_format($totalClients) }}</h2>
                        <p class="mb-0 mt-2 text-muted fs-sm">عميل مسجل في النظام</p>
                    </div>
                    <div class="avatar-md">
                        <div class="avatar-title bg-primary-subtle text-primary rounded fs-22">
                            <i class="ti ti-users fs-24"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center card-footer bg-primary-subtle border-0">
                <a href="#" class="text-primary fw-semibold text-decoration-none">
                    <i class="ti ti-eye me-1"></i> عرض جميع العملاء
                </a>
            </div>
        </div>
    </div>

    {{-- Total Pages Card --}}
    <div class="col">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="mb-3 text-uppercase text-muted">إجمالي الصفحات</h5>
                        <h2 class="mb-0 fw-bold text-success">{{ number_format($totalPages) }}</h2>
                        <p class="mb-0 mt-2 text-muted fs-sm">صفحة مؤرشفة في النظام</p>
                    </div>
                    <div class="avatar-md">
                        <div class="avatar-title bg-success-subtle text-success rounded fs-22">
                            <i class="ti ti-file-text fs-24"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center card-footer bg-success-subtle border-0">
                <span class="text-success fw-semibold">
                    <i class="ti ti-chart-bar me-1"></i> إحصائيات الأرشيف
                </span>
            </div>
        </div>
    </div>

    {{-- Total Files Card --}}
    <div class="col">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="mb-3 text-uppercase text-muted">إجمالي الملفات</h5>
                        <h2 class="mb-0 fw-bold text-info">{{ number_format($totalFiles) }}</h2>
                        <p class="mb-0 mt-2 text-muted fs-sm">ملف رئيسي في النظام</p>
                    </div>
                    <div class="avatar-md">
                        <div class="avatar-title bg-info-subtle text-info rounded fs-22">
                            <i class="ti ti-folders fs-24"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center card-footer bg-info-subtle border-0">
                <a href="#" class="text-info fw-semibold text-decoration-none">
                    <i class="ti ti-eye me-1"></i> عرض جميع الملفات
                </a>
            </div>
        </div>
    </div>
</div>
