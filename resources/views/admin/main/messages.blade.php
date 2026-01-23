<!-- Toast Container -->
<div class="top-0 p-3 toast-container position-fixed end-0" style="z-index: 9999;">

    @if(session('success'))
        <div class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
            <div class="toast-header bg-success-subtle">
                <i class="ti ti-check-circle text-success me-2"></i>
                <strong class="me-auto text-success">Success</strong>
                <small class="text-muted">just now</small>
                <button type="button" class="ms-2 btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
            <div class="toast-header bg-danger-subtle">
                <i class="ti ti-alert-circle text-danger me-2"></i>
                <strong class="me-auto text-danger">Error</strong>
                <small class="text-muted">just now</small>
                <button type="button" class="ms-2 btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                {{ session('error') }}
            </div>
        </div>
    @endif

    @if($errors->any())
        @foreach($errors->all() as $error)
            <div class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
                <div class="toast-header bg-danger-subtle">
                    <i class="ti ti-alert-circle text-danger me-2"></i>
                    <strong class="me-auto text-danger">Validation Error</strong>
                    <small class="text-muted">just now</small>
                    <button type="button" class="ms-2 btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ $error }}
                </div>
            </div>
        @endforeach
    @endif

</div>

<script>
    // Auto-hide toasts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        var toastElList = [].slice.call(document.querySelectorAll('.toast'));
        var toastList = toastElList.map(function(toastEl) {
            return new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 5000
            });
        });
        toastList.forEach(toast => toast.show());
    });
</script>
