<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0" id="detailsContent">
                <div class="py-5 text-center">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Clear Old Logs Modal -->
@can('activity-logs.delete')
<div class="modal fade" id="clearOldModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-trash me-2"></i>حذف السجلات القديمة
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.activity-logs.clear-old') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">حذف السجلات الأقدم من</label>
                        <select name="days" class="form-select">
                            <option value="30">30 يوم</option>
                            <option value="60">60 يوم</option>
                            <option value="90" selected>90 يوم</option>
                            <option value="180">180 يوم</option>
                            <option value="365">سنة</option>
                        </select>
                    </div>
                    <div class="alert alert-warning">
                        <i class="ti ti-alert-triangle me-2"></i>
                        هذا الإجراء لا يمكن التراجع عنه!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="ti ti-trash me-1"></i>حذف
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
