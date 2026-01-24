<div class="border-0 shadow-sm card">
    <div class="bg-white card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 card-title">
            <i class="ti ti-list me-2"></i>سجل النشاطات
            <span class="badge bg-primary ms-2">{{ $logs->total() }}</span>
        </h5>
        @can('activity-logs.delete')
        <div class="gap-2 d-flex">
            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#clearOldModal">
                <i class="ti ti-trash me-1"></i>حذف السجلات القديمة
            </button>
        </div>
        @endcan
    </div>
    <div class="p-0 card-body">
        @if($logs->count() > 0)
        @foreach($logs as $log)
        <div class="px-4 py-3 border-bottom activity-log-row" style="transition: background 0.2s;">
            <div class="gap-4 d-flex align-items-center">
                {{-- Timestamp --}}
                <div class="text-center" style="min-width: 80px;">
                    <div class="text-dark" style="font-size: 24px; font-weight: 700; line-height: 1; font-family: 'SF Mono', 'Consolas', monospace;">
                        {{ $log->created_at->format('H:i') }}
                    </div>
                    <div class="text-muted" style="font-size: 12px;">{{ $log->created_at->format('Y/m/d') }}</div>
                </div>

                {{-- Action Icon --}}
                <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary-subtle"
                     style="width: 48px; height: 48px; flex-shrink: 0;">
                    <i class="ti {{ $log->action_type_icon }} text-primary" style="font-size: 22px;"></i>
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
                {{-- User --}}
                <div class="text-end" style="min-width: 140px;">
                    @if($log->user)
                    <a href="{{ route('admin.activity-logs.user-timeline', $log->user->id) }}" class="text-decoration-none">
                        <div class="fw-semibold text-dark">{{ $log->user->name }}</div>
                    </a>
                    <div class="text-muted" style="font-size: 11px; font-family: monospace;">{{ $log->ip_address ?? '-' }}</div>
                    @else
                    <div class="text-muted">
                        <i class="ti ti-robot me-1"></i>النظام
                    </div>
                    @endif
                </div>
                {{-- Details Button --}}
                <button type="button" class="btn btn-outline-primary btn-view-details" data-log-id="{{ $log->id }}" style="width: 36px; height: 36px; padding: 0;">
                    <i class="ti ti-eye fs-4"></i>
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
            <h5 class="text-muted">لا توجد سجلات</h5>
            <p class="text-muted">لم يتم العثور على أي نشاطات مطابقة للفلترة</p>
        </div>
        @endif
    </div>
</div>

<style>
.activity-log-row:hover {
    background-color: #f8f9fa;
}
</style>
