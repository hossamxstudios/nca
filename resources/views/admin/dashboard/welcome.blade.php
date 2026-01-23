{{-- Welcome Section --}}
<div class="py-4 mb-2 row justify-content-center">
    <div class="text-center col-xxl-5 col-xl-7">
        <span class="px-3 py-2 mb-3 shadow-sm badge bg-primary-subtle text-primary fw-semibold fs-xs">
            <i class="ti ti-archive me-1"></i> نظام أرشيف القاهرة الجديدة
        </span>
        <h3 class="mb-2 fw-bold">
            مرحباً بك، {{ Auth::user()->first_name }}! 👋
        </h3>
        <p class="mb-0 fs-sm text-muted">
            لوحة التحكم الرئيسية لإدارة الأرشيف والملفات والعملاء
        </p>
    </div>
</div>
