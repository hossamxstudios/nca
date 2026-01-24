<div class="mb-4 row g-3">
    <div class="col-md-3">
        <div class="border-0 shadow-sm card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded avatar avatar-lg bg-primary-subtle d-flex align-items-center justify-content-center">
                            <i class="ti ti-activity fs-4 text-primary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h3 class="mb-0">{{ number_format($stats['total']) }}</h3>
                        <p class="mb-0 text-muted">إجمالي السجلات</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="border-0 shadow-sm card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded avatar avatar-lg bg-success-subtle d-flex align-items-center justify-content-center">
                            <i class="ti ti-calendar-event fs-4 text-success"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h3 class="mb-0">{{ number_format($stats['today']) }}</h3>
                        <p class="mb-0 text-muted">نشاط اليوم</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="border-0 shadow-sm card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded avatar avatar-lg bg-info-subtle d-flex align-items-center justify-content-center">
                            <i class="ti ti-calendar-week fs-4 text-info"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h3 class="mb-0">{{ number_format($stats['this_week']) }}</h3>
                        <p class="mb-0 text-muted">نشاط الأسبوع</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="border-0 shadow-sm card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded avatar avatar-lg bg-warning-subtle d-flex align-items-center justify-content-center">
                            <i class="ti ti-login fs-4 text-warning"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h3 class="mb-0">{{ number_format($stats['logins_today']) }}</h3>
                        <p class="mb-0 text-muted">تسجيلات دخول اليوم</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
