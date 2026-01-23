<div class="mb-1 row">
    <div class="col-xl-4 col-md-6">
        <div class="border-0 shadow card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-3">
                                <i class="ti ti-users"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="mb-1 text-muted fs-sm">إجمالي العملاء</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($totalClients) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="border-0 shadow card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-success-subtle text-success rounded-circle fs-3">
                                <i class="ti ti-folders"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="mb-1 text-muted fs-sm">إجمالي الملفات</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($totalFiles) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="border-0 shadow card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-info-subtle text-info rounded-circle fs-3">
                                <i class="ti ti-file-text"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="mb-1 text-muted fs-sm">إجمالي الصفحات</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($totalPages) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
