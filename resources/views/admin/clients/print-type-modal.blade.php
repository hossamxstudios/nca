<!-- Print Type Selection Modal -->
<div class="modal fade" id="printTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-printer me-2"></i>طباعة الباركود
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 text-center">
                    <h6 class="mb-1 text-muted">عدد الملفات المحددة</h6>
                    <strong id="printFilesCount">0</strong> ملف
                </div>
                <div class="mt-4">
                    <label class="mb-2 text-center form-label text-muted d-block">
                        <i class="ti ti-settings me-1"></i>خيارات الطباعة
                    </label>
                    <div class="gap-3 d-flex justify-content-center">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="bulkPrintOption" id="printAllOption" value="all" checked>
                            <label class="form-check-label" for="printAllOption">
                                <strong id="printAllCount">0</strong> استيكر (كل الصفحات)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="bulkPrintOption" id="printFirstOption" value="first">
                            <label class="form-check-label" for="printFirstOption">
                                <strong id="printFirstCount">0</strong> استيكر (أول صفحة فقط)
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>إغلاق
                </button>
                <button type="button" class="btn btn-primary" onclick="selectPrintType(document.querySelector('input[name=bulkPrintOption]:checked').value)">
                    <i class="ti ti-printer me-1"></i>طباعة
                </button>
            </div>
        </div>
    </div>
</div>
