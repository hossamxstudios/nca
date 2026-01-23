{{-- Latest Clients Table Section --}}
<div class="card">
    <div class="border-dashed card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0 card-title">
            <i class="ti ti-users me-2 text-primary"></i> آخر العملاء المضافين
        </h4>
        <a href="#" class="btn btn-sm btn-primary">
            <i class="ti ti-eye me-1"></i> عرض الكل
        </a>
    </div>

    <div class="p-0 card-body">
        <div class="table-responsive">
            <table class="table mb-0 table-centered table-hover table-nowrap">
                <thead class="bg-light-subtle">
                    <tr class="text-uppercase fs-xs">
                        <th class="text-center" style="width: 50px;">#</th>
                        <th>اسم العميل</th>
                        <th>كود العميل</th>
                        <th>رقم الهوية</th>
                        <th>الموقع</th>
                        <th>تاريخ الإضافة</th>
                        <th class="text-center" style="width: 80px;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestClients as $index => $client)
                    <tr>
                        <td class="text-center">
                            <span class="badge bg-dark-subtle text-dark">{{ $index + 1 }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-2">
                                    <span class="fw-bold">{{ mb_substr($client->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold">{{ $client->name }}</h6>
                                    @if($client->mobile)
                                        <small class="text-muted">{{ $client->mobile }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($client->client_code)
                                <span class="badge bg-secondary-subtle text-secondary">{{ $client->client_code }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($client->national_id)
                                <code class="text-dark">{{ $client->national_id }}</code>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $firstLand = $client->lands->first();
                            @endphp
                            @if($firstLand && $firstLand->governorate)
                                <span class="text-muted">
                                    <i class="ti ti-map-pin me-1"></i>
                                    {{ $firstLand->governorate->name }}
                                    @if($firstLand->city)
                                        - {{ $firstLand->city->name }}
                                    @endif
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted">
                                <i class="ti ti-calendar me-1"></i>
                                {{ $client->created_at->format('Y-m-d') }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="dropdown">
                                <a href="#" class="p-0 dropdown-toggle text-muted drop-arrow-none" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical fs-lg"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="#" class="dropdown-item">
                                        <i class="ti ti-eye me-2"></i> عرض التفاصيل
                                    </a>
                                    <a href="#" class="dropdown-item">
                                        <i class="ti ti-edit me-2"></i> تعديل
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-5 text-center">
                            <div class="text-muted">
                                <i class="ti ti-users-minus fs-48 d-block mb-3 opacity-50"></i>
                                <h5 class="mb-1">لا يوجد عملاء حتى الآن</h5>
                                <p class="mb-3">ابدأ بإضافة عميل جديد للنظام</p>
                                <a href="#" class="btn btn-sm btn-primary">
                                    <i class="ti ti-plus me-1"></i> إضافة عميل
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($latestClients->count() > 0)
    <div class="text-center card-footer border-0">
        <a href="#" class="btn btn-sm btn-outline-primary">
            عرض جميع العملاء <i class="ti ti-arrow-left ms-1"></i>
        </a>
    </div>
    @endif
</div>
