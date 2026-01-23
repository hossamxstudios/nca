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
                        <p class="mb-1 text-muted fs-sm">إجمالي المستخدمين</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($totalUsers) }}</h4>
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
                                <i class="ti ti-user-check"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="mb-1 text-muted fs-sm">المستخدمين النشطين</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($activeUsers) }}</h4>
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
                            <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-3">
                                <i class="ti ti-trash"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="mb-1 text-muted fs-sm">المحذوفين</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($trashedUsers) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
