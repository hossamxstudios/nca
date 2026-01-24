<!DOCTYPE html>
@include('admin.main.html')
<head>
    <title>سجل نشاط {{ $user->name }} - أرشيف القاهرة الجديدة</title>
    @include('admin.main.meta')
</head>
<body>
    <div class="wrapper">
        @include('admin.main.topbar')
        @include('admin.main.sidebar')
        <div class="content-page">
            <div class="container-fluid">
                <!-- Header -->
                <div class="mt-3 mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="ti ti-user-check me-2"></i>سجل نشاط المستخدم
                        </h4>
                        <nav aria-label="breadcrumb">
                            <ol class="mb-0 breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.activity-logs.index') }}">سجل النشاطات</a></li>
                                <li class="breadcrumb-item active">{{ $user->name }}</li>
                            </ol>
                        </nav>
                    </div>
                    <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary btn-sm">
                        <i class="ti ti-arrow-right me-1"></i>العودة
                    </a>
                </div>

                <!-- User Info Card -->
                <div class="mb-4 border-0 shadow-sm card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle avatar avatar-lg bg-primary-subtle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                    <span class="text-primary fs-4">{{ $user->initials }}</span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1">{{ $user->name }}</h5>
                                <p class="mb-0 text-muted">{{ $user->email }}</p>
                                @if($user->roles->count() > 0)
                                    <span class="badge bg-primary-subtle text-primary">{{ $user->roles->first()->name }}</span>
                                @endif
                            </div>
                            <div class="text-end">
                                <div class="mb-1 fs-4 fw-bold text-primary">{{ $logs->total() }}</div>
                                <small class="text-muted">إجمالي النشاطات</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="border-0 shadow-sm card">
                    <div class="bg-white card-header">
                        <h5 class="mb-0 card-title">
                            <i class="ti ti-timeline me-2"></i>سجل النشاطات
                            <span class="badge bg-primary ms-2">{{ $logs->total() }}</span>
                        </h5>
                    </div>
                    <div class="p-0 card-body">
                        @if($logs->count() > 0)
                        @php $currentDate = null; @endphp
                        @foreach($logs as $log)
                            @if($currentDate !== $log->created_at->format('Y-m-d'))
                                @php $currentDate = $log->created_at->format('Y-m-d'); @endphp
                                <div class="px-4 py-2 bg-light border-bottom">
                                    <span class="fw-semibold text-dark">{{ $log->created_at->format('Y/m/d') }}</span>
                                    <span class="text-muted ms-2">{{ $log->created_at->translatedFormat('l') }}</span>
                                </div>
                            @endif
                            <div class="px-4 py-3 border-bottom activity-log-row" style="transition: background 0.2s;">
                                <div class="gap-4 d-flex align-items-center">
                                    {{-- Timestamp --}}
                                    <div class="text-center" style="min-width: 70px;">
                                        <div class="text-dark" style="font-size: 22px; font-weight: 700; line-height: 1; font-family: 'SF Mono', 'Consolas', monospace;">
                                            {{ $log->created_at->format('H:i') }}
                                        </div>
                                    </div>

                                    {{-- Action Icon --}}
                                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary-subtle"
                                         style="width: 44px; height: 44px; flex-shrink: 0;">
                                        <i class="ti {{ $log->action_type_icon }} text-primary" style="font-size: 20px;"></i>
                                    </div>

                                    {{-- Action & Description --}}
                                    <div class="flex-grow-1">
                                        <div class="gap-2 mb-1 d-flex align-items-center">
                                            <span class="fw-bold" style="font-size: 15px;">{{ $log->action_type_label }}</span>
                                            <span class="text-muted">•</span>
                                            <span class="text-muted" style="font-size: 13px;">{{ $log->action_group_label }}</span>
                                            @if($log->batch_count)
                                            <span class="badge bg-dark rounded-pill">{{ $log->batch_count }}</span>
                                            @endif
                                        </div>
                                        <div class="text-secondary" style="font-size: 14px;">{{ $log->description }}</div>
                                        @if($log->subject_name)
                                        <div class="mt-1">
                                            <span class="border badge bg-light text-dark" style="font-weight: 500;">
                                                <i class="ti ti-tag me-1" style="font-size: 11px;"></i>{{ $log->subject_name }}
                                            </span>
                                        </div>
                                        @endif
                                    </div>

                                    {{-- IP Address --}}
                                    <div class="text-end" style="min-width: 120px;">
                                        <div class="text-muted" style="font-size: 11px; font-family: monospace;">{{ $log->ip_address ?? '-' }}</div>
                                    </div>

                                    {{-- Details Button --}}
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-view-details" data-log-id="{{ $log->id }}" style="width: 36px; height: 36px; padding: 0;">
                                        <i class="ti ti-info-circle"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                        <div class="p-3">
                            {{ $logs->links() }}
                        </div>
                        @else
                        <div class="py-5 text-center">
                            <i class="mb-3 ti ti-activity text-muted" style="font-size: 48px;"></i>
                            <h5 class="text-muted">لا توجد نشاطات</h5>
                            <p class="text-muted">لم يتم تسجيل أي نشاط لهذا المستخدم</p>
                        </div>
                        @endif
                    </div>
                </div>

                <style>
                .activity-log-row:hover {
                    background-color: #f8f9fa;
                }
                </style>
                @include('admin.activity-logs.modals')
            </div>
        </div>
    </div>
    @include('admin.main.scripts')
    @include('admin.activity-logs.scripts')
</body>
</html>
