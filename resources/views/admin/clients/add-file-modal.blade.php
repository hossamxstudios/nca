@php
    $rightColumnCount = 37;
    $rightColumnItems = $items->slice(0, $rightColumnCount);
    $leftColumnItems = $items->slice($rightColumnCount);
    $maxRows = max($rightColumnItems->count(), $leftColumnItems->count());
@endphp

@foreach($clients as $client)
<div class="modal fade" id="addFileModal_{{ $client->id }}" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="pt-2 pb-1 modal-header bg-primary-subtle">
                <h5 class="modal-title">
                    <i class="ti ti-file-plus me-2"></i>إضافة ملف جديد
                    <span class="badge bg-primary ms-2">{{ $client->name }}</span>
                    <span class="badge bg-info ms-2 add-file-pages-info" data-client="{{ $client->id }}">0 صفحة</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.files.store', $client->id) }}" method="POST" enctype="multipart/form-data" class="overflow-hidden d-flex flex-column flex-grow-1 add-file-form" data-client-id="{{ $client->id }}">
                @csrf
                <div class="overflow-auto p-3 modal-body">
                    <div class="row">
                        {{-- Left Side - PDF Preview --}}
                        <div class="col-md-4">
                            <div class="p-3 rounded border pdf-preview-container d-flex flex-column align-items-center bg-light" style="position: sticky; top: 0; height: calc(100vh - 180px); overflow: hidden;">
                                {{-- Rotation Controls --}}
                                <div class="mb-2 btn-group btn-group-sm d-none add-file-rotation-controls" data-client="{{ $client->id }}" style="position: relative; z-index: 10;">
                                    <button type="button" class="btn btn-outline-secondary add-file-rotate-left" data-client="{{ $client->id }}" title="تدوير لليسار">
                                        <i class="ti ti-rotate-2"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary add-file-rotate-right" data-client="{{ $client->id }}" title="تدوير لليمين">
                                        <i class="ti ti-rotate-clockwise-2"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary add-file-rotate-reset" data-client="{{ $client->id }}" title="إعادة تعيين">
                                        <i class="ti ti-refresh"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="rotation" class="add-file-rotation" data-client="{{ $client->id }}" value="0">
                                <div class="text-center add-file-pdf-placeholder w-100" data-client="{{ $client->id }}">
                                    <i class="ti ti-file-type-pdf text-muted" style="font-size: 5rem;"></i>
                                    <p class="mt-3 text-muted">معاينة الصفحة الأولى ستظهر هنا</p>
                                </div>
                                <div class="flex-grow-1 d-flex align-items-center justify-content-center w-100" style="overflow: auto;">
                                    <canvas class="d-none add-file-canvas" data-client="{{ $client->id }}" style="transition: transform 0.3s ease; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>

                        {{-- Right Side - Form --}}
                        <div class="col-md-8">
                            {{-- File Info Section --}}
                            <div class="p-3 mb-3 rounded border bg-light">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">
                                            <i class="ti ti-file me-1"></i>رقم الملف المستلم <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" name="file_name" required placeholder="رقم الملف">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">
                                            <i class="ti ti-upload me-1"></i>ملف PDF الأساسي <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control add-file-pdf-input" name="pdf_file" accept=".pdf" required data-client="{{ $client->id }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">
                                            <i class="ti ti-file-plus me-1"></i>ملفات إضافية للدمج
                                        </label>
                                        <input type="file" class="form-control add-file-extra-pdfs" name="extra_pdf_files[]" accept=".pdf" multiple data-client="{{ $client->id }}" disabled>
                                        <small class="text-muted">اختر الملف الأساسي أولاً</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Location Sections as Accordions --}}
                            <div class="mb-3 accordion" id="locationAccordion_{{ $client->id }}">
                                {{-- Geolocation Section --}}
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="py-2 accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#geoCollapse_{{ $client->id }}">
                                            <i class="ti ti-map-pin me-2 text-primary"></i>
                                            <span class="fw-bold">العنوان الجغرافي</span>
                                            <span class="badge bg-danger ms-2">مطلوب</span>
                                        </button>
                                    </h2>
                                    <div id="geoCollapse_{{ $client->id }}" class="accordion-collapse collapse" data-bs-parent="#locationAccordion_{{ $client->id }}">
                                        <div class="py-2 accordion-body">
                                            <div class="row g-2">
                                                <div class="col-md-3">
                                                    <label class="form-label small">الحي <span class="text-danger">*</span></label>
                                                    <select class="form-select form-select-sm add-file-district" name="district_id" data-client="{{ $client->id }}">
                                                        <option value="">اختر</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">المجاورة <span class="text-danger">*</span></label>
                                                    <select class="form-select form-select-sm add-file-zone" name="zone_id" data-client="{{ $client->id }}" disabled>
                                                        <option value="">اختر</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">المنطقة</label>
                                                    <select class="form-select form-select-sm add-file-area" name="area_id" data-client="{{ $client->id }}" disabled>
                                                        <option value="">اختر</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">رقم القطعة <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm" name="land_no" placeholder="القطعة">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Physical Location Section --}}
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="py-2 accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#physicalCollapse_{{ $client->id }}">
                                            <i class="ti ti-building me-2 text-info"></i>
                                            <span class="fw-bold">الموقع الفعلي</span>
                                            <span class="badge bg-secondary ms-2">اختياري</span>
                                        </button>
                                    </h2>
                                    <div id="physicalCollapse_{{ $client->id }}" class="accordion-collapse collapse" data-bs-parent="#locationAccordion_{{ $client->id }}">
                                        <div class="py-2 accordion-body">
                                            <div class="row g-2">
                                                <div class="col-md-3">
                                                    <label class="form-label small">الغرفة</label>
                                                    <select class="form-select form-select-sm add-file-room" name="room_id" data-client="{{ $client->id }}">
                                                        <option value="">اختر</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">الممر</label>
                                                    <select class="form-select form-select-sm add-file-lane" name="lane_id" data-client="{{ $client->id }}" disabled>
                                                        <option value="">اختر</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">الاستاند</label>
                                                    <select class="form-select form-select-sm add-file-stand" name="stand_id" data-client="{{ $client->id }}" disabled>
                                                        <option value="">اختر</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">الرف</label>
                                                    <select class="form-select form-select-sm add-file-rack" name="rack_id" data-client="{{ $client->id }}" disabled>
                                                        <option value="">اختر</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Items Table --}}
                            <div class="rounded border">
                                <table class="table mb-0 align-middle table-sm table-bordered" style="--bs-table-cell-padding-y: 0.15rem;">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th style="width: 70px;">إلى</th>
                                            <th style="width: 70px;">من</th>
                                            <th>توصيف المستند</th>
                                            <th style="width: 35px;"></th>
                                            <th style="width: 70px;">إلى</th>
                                            <th style="width: 70px;">من</th>
                                            <th>توصيف المستند</th>
                                            <th style="width: 35px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for($i = 0; $i < $maxRows; $i++)
                                        @php
                                            $rightItem = $rightColumnItems->values()->get($i);
                                            $leftItem = $leftColumnItems->values()->get($i);
                                        @endphp
                                        <tr>
                                            @if($leftItem)
                                            <td class="py-0">
                                                <select class="form-select form-select-sm add-file-page-to" name="items[{{ $leftItem->id }}][to_page]" data-client="{{ $client->id }}" data-item="{{ $leftItem->id }}" disabled>
                                                    <option value="">إلى</option>
                                                </select>
                                            </td>
                                            <td class="py-0">
                                                <select class="form-select form-select-sm add-file-page-from" name="items[{{ $leftItem->id }}][from_page]" data-client="{{ $client->id }}" data-item="{{ $leftItem->id }}" disabled>
                                                    <option value="">من</option>
                                                </select>
                                            </td>
                                            <td class="py-0">
                                                <label for="addFileItem_{{ $client->id }}_{{ $leftItem->id }}" class="mb-0 cursor-pointer">{{ $leftItem->name }}</label>
                                            </td>
                                            <td class="py-0">
                                                <div class="form-check form-switch fs-xxl">
                                                    <input class="form-check-input add-file-item-toggle" type="checkbox" name="items[{{ $leftItem->id }}][enabled]" id="addFileItem_{{ $client->id }}_{{ $leftItem->id }}" data-client="{{ $client->id }}" data-item="{{ $leftItem->id }}">
                                                </div>
                                            </td>
                                            @else
                                            <td colspan="4"></td>
                                            @endif

                                            @if($rightItem)
                                            <td class="py-0">
                                                <select class="form-select form-select-sm add-file-page-to" name="items[{{ $rightItem->id }}][to_page]" data-client="{{ $client->id }}" data-item="{{ $rightItem->id }}" disabled>
                                                    <option value="">إلى</option>
                                                </select>
                                            </td>
                                            <td class="py-0">
                                                <select class="form-select form-select-sm add-file-page-from" name="items[{{ $rightItem->id }}][from_page]" data-client="{{ $client->id }}" data-item="{{ $rightItem->id }}" disabled>
                                                    <option value="">من</option>
                                                </select>
                                            </td>
                                            <td class="py-0">
                                                <label for="addFileItem_{{ $client->id }}_{{ $rightItem->id }}" class="mb-0 cursor-pointer">{{ $rightItem->name }}</label>
                                            </td>
                                            <td class="py-0">
                                                <div class="form-check form-switch fs-xxl">
                                                    <input class="form-check-input add-file-item-toggle" type="checkbox" name="items[{{ $rightItem->id }}][enabled]" id="addFileItem_{{ $client->id }}_{{ $rightItem->id }}" data-client="{{ $client->id }}" data-item="{{ $rightItem->id }}">
                                                </div>
                                            </td>
                                            @else
                                            <td colspan="4"></td>
                                            @endif
                                        </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pt-0 pb-0 modal-footer">
                    <button type="button" class="btn bg-danger-subtle" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>إلغاء
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>إضافة ورفع الملف
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<style>
.add-file-page-from, .add-file-page-to {
    min-width: 65px !important;
    text-align: center;
    padding-right: 20px !important;
    padding-left: 6px !important;
}
.add-file-page-from:disabled, .add-file-page-to:disabled {
    background-color: #e9ecef;
    opacity: 0.5;
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    }

    const addFileUsedRanges = {};

    // Load districts and rooms on modal show
    document.querySelectorAll('[id^="addFileModal_"]').forEach(modal => {
        modal.addEventListener('show.bs.modal', function() {
            const clientId = this.id.replace('addFileModal_', '');
            loadAddFileDistricts(clientId);
            loadAddFileRooms(clientId);
        });

        modal.addEventListener('hidden.bs.modal', function() {
            const clientId = this.id.replace('addFileModal_', '');
            resetAddFileModal(clientId);
        });
    });

    // Form validation on submit
    document.querySelectorAll('.add-file-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const clientId = this.dataset.clientId;
            const modal = document.querySelector(`#addFileModal_${clientId}`);
            const districtSelect = modal.querySelector('.add-file-district');
            const zoneSelect = modal.querySelector('.add-file-zone');
            const landNoInput = modal.querySelector('input[name="land_no"]');

            // Check if geo fields are filled
            let hasError = false;

            // Reset validation states
            districtSelect.classList.remove('is-invalid');
            zoneSelect.classList.remove('is-invalid');
            landNoInput.classList.remove('is-invalid');

            if (!districtSelect.value) {
                hasError = true;
                districtSelect.classList.add('is-invalid');
            }

            if (!zoneSelect.value) {
                hasError = true;
                zoneSelect.classList.add('is-invalid');
            }

            if (!landNoInput.value.trim()) {
                hasError = true;
                landNoInput.classList.add('is-invalid');
            }

            if (hasError) {
                e.preventDefault();

                // Open the geo accordion
                const geoCollapse = modal.querySelector(`#geoCollapse_${clientId}`);
                const geoAccordionBtn = modal.querySelector(`[data-bs-target="#geoCollapse_${clientId}"]`);

                if (!geoCollapse.classList.contains('show')) {
                    geoAccordionBtn.classList.remove('collapsed');
                    geoCollapse.classList.add('show');
                }

                // Show error alert
                let alertDiv = modal.querySelector('.geo-validation-alert');
                if (!alertDiv) {
                    alertDiv = document.createElement('div');
                    alertDiv.className = 'geo-validation-alert alert alert-danger alert-dismissible fade show mb-3';
                    alertDiv.innerHTML = `
                        <i class="ti ti-alert-circle me-2"></i>
                        <strong>خطأ:</strong> يجب ملء بيانات العنوان الجغرافي (الحي، المجاورة، رقم القطعة)
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    modal.querySelector('.modal-body .row').insertBefore(alertDiv, modal.querySelector('.modal-body .row').firstChild);
                }
                alertDiv.style.display = 'block';

                // Scroll to top of modal
                modal.querySelector('.modal-body').scrollTop = 0;

                return false;
            }

            // Hide error alert if exists and validation passed
            const alertDiv = modal.querySelector('.geo-validation-alert');
            if (alertDiv) alertDiv.style.display = 'none';

            // Form is valid - allow submission
            return true;
        });
    });

    // Store main file page counts
    const addFileMainPages = {};

    // PDF file input change (main file)
    document.querySelectorAll('.add-file-pdf-input').forEach(input => {
        input.addEventListener('change', function(e) {
            const clientId = this.dataset.client;
            const file = e.target.files[0];
            const extraInput = document.querySelector(`.add-file-extra-pdfs[data-client="${clientId}"]`);

            if (file && file.type === 'application/pdf') {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const typedArray = new Uint8Array(e.target.result);
                    pdfjsLib.getDocument(typedArray).promise.then(function(pdf) {
                        const totalPages = pdf.numPages;
                        addFileMainPages[clientId] = totalPages;
                        document.querySelector(`.add-file-pages-info[data-client="${clientId}"]`).textContent = totalPages + ' صفحة';
                        document.querySelector(`#addFileModal_${clientId}`).dataset.totalPages = totalPages;
                        addFileUsedRanges[clientId] = [];
                        updateAddFilePageSelects(clientId, totalPages);

                        // Enable extra files input
                        extraInput.disabled = false;
                        extraInput.previousElementSibling.nextElementSibling.textContent = 'اختر ملفات PDF إضافية للدمج';

                        pdf.getPage(1).then(function(page) {
                            const canvas = document.querySelector(`.add-file-canvas[data-client="${clientId}"]`);
                            const context = canvas.getContext('2d');
                            const viewport = page.getViewport({ scale: 1.0 });
                            const containerWidth = canvas.parentElement.offsetWidth - 40;
                            const scale = containerWidth / viewport.width;
                            const scaledViewport = page.getViewport({ scale: scale });
                            canvas.height = scaledViewport.height;
                            canvas.width = scaledViewport.width;
                            page.render({ canvasContext: context, viewport: scaledViewport });
                            canvas.classList.remove('d-none');
                            document.querySelector(`.add-file-pdf-placeholder[data-client="${clientId}"]`).classList.add('d-none');
                            document.querySelector(`.add-file-rotation-controls[data-client="${clientId}"]`).classList.remove('d-none');
                        });
                    });
                };
                reader.readAsArrayBuffer(file);
            } else {
                // Disable extra files if main file removed
                extraInput.disabled = true;
                extraInput.value = '';
                extraInput.previousElementSibling.nextElementSibling.textContent = 'اختر الملف الأساسي أولاً';
            }
        });
    });

    // Extra PDF files input change
    document.querySelectorAll('.add-file-extra-pdfs').forEach(input => {
        input.addEventListener('change', async function(e) {
            const clientId = this.dataset.client;
            const files = e.target.files;
            const mainPages = addFileMainPages[clientId] || 0;

            if (files.length > 0 && mainPages > 0) {
                let extraPages = 0;

                // Count pages from all extra files
                for (const file of files) {
                    if (file.type === 'application/pdf') {
                        const arrayBuffer = await file.arrayBuffer();
                        const typedArray = new Uint8Array(arrayBuffer);
                        const pdf = await pdfjsLib.getDocument(typedArray).promise;
                        extraPages += pdf.numPages;
                    }
                }

                const totalPages = mainPages + extraPages;
                document.querySelector(`.add-file-pages-info[data-client="${clientId}"]`).textContent =
                    `${totalPages} صفحة (${mainPages} أساسي + ${extraPages} إضافي)`;
                document.querySelector(`#addFileModal_${clientId}`).dataset.totalPages = totalPages;
                updateAddFilePageSelects(clientId, totalPages);
            }
        });
    });

    // Item toggle
    document.querySelectorAll('.add-file-item-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const clientId = this.dataset.client;
            const itemId = this.dataset.item;
            const fromSelect = document.querySelector(`.add-file-page-from[data-client="${clientId}"][data-item="${itemId}"]`);
            const toSelect = document.querySelector(`.add-file-page-to[data-client="${clientId}"][data-item="${itemId}"]`);
            if (this.checked) {
                fromSelect.disabled = false;
                toSelect.disabled = false;
                fromSelect.required = true;
                toSelect.required = true;
                updateAddFileAvailablePages(clientId);
            } else {
                fromSelect.disabled = true;
                toSelect.disabled = true;
                fromSelect.required = false;
                toSelect.required = false;
                fromSelect.value = '';
                toSelect.value = '';
                updateAddFileUsedRanges(clientId);
                updateAddFileAvailablePages(clientId);
            }
        });
    });

    // Page from/to change
    document.querySelectorAll('.add-file-page-from').forEach(select => {
        select.addEventListener('change', function() {
            const clientId = this.dataset.client;
            const itemId = this.dataset.item;
            const toSelect = document.querySelector(`.add-file-page-to[data-client="${clientId}"][data-item="${itemId}"]`);
            if (this.value && (!toSelect.value || parseInt(toSelect.value) < parseInt(this.value))) {
                toSelect.value = this.value;
            }
            updateAddFileUsedRanges(clientId);
            updateAddFileAvailablePages(clientId);
        });
    });

    document.querySelectorAll('.add-file-page-to').forEach(select => {
        select.addEventListener('change', function() {
            const clientId = this.dataset.client;
            updateAddFileUsedRanges(clientId);
            updateAddFileAvailablePages(clientId);
        });
    });

    // Cascading dropdowns - District
    document.querySelectorAll('.add-file-district').forEach(select => {
        select.addEventListener('change', function() {
            const clientId = this.dataset.client;
            const zoneSelect = document.querySelector(`.add-file-zone[data-client="${clientId}"]`);
            const areaSelect = document.querySelector(`.add-file-area[data-client="${clientId}"]`);
            zoneSelect.innerHTML = '<option value="">اختر</option>';
            areaSelect.innerHTML = '<option value="">اختر</option>';
            zoneSelect.disabled = true;
            areaSelect.disabled = true;
            if (this.value) loadAddFileZones(clientId, this.value);
        });
    });

    // Zone
    document.querySelectorAll('.add-file-zone').forEach(select => {
        select.addEventListener('change', function() {
            const clientId = this.dataset.client;
            const areaSelect = document.querySelector(`.add-file-area[data-client="${clientId}"]`);
            areaSelect.innerHTML = '<option value="">اختر</option>';
            areaSelect.disabled = true;
            if (this.value) loadAddFileAreas(clientId, this.value);
        });
    });

    // Room
    document.querySelectorAll('.add-file-room').forEach(select => {
        select.addEventListener('change', function() {
            const clientId = this.dataset.client;
            const laneSelect = document.querySelector(`.add-file-lane[data-client="${clientId}"]`);
            const standSelect = document.querySelector(`.add-file-stand[data-client="${clientId}"]`);
            const rackSelect = document.querySelector(`.add-file-rack[data-client="${clientId}"]`);
            laneSelect.innerHTML = '<option value="">اختر</option>';
            standSelect.innerHTML = '<option value="">اختر</option>';
            rackSelect.innerHTML = '<option value="">اختر</option>';
            laneSelect.disabled = true;
            standSelect.disabled = true;
            rackSelect.disabled = true;
            if (this.value) loadAddFileLanes(clientId, this.value);
        });
    });

    // Lane
    document.querySelectorAll('.add-file-lane').forEach(select => {
        select.addEventListener('change', function() {
            const clientId = this.dataset.client;
            const standSelect = document.querySelector(`.add-file-stand[data-client="${clientId}"]`);
            const rackSelect = document.querySelector(`.add-file-rack[data-client="${clientId}"]`);
            standSelect.innerHTML = '<option value="">اختر</option>';
            rackSelect.innerHTML = '<option value="">اختر</option>';
            standSelect.disabled = true;
            rackSelect.disabled = true;
            if (this.value) loadAddFileStands(clientId, this.value);
        });
    });

    // Stand
    document.querySelectorAll('.add-file-stand').forEach(select => {
        select.addEventListener('change', function() {
            const clientId = this.dataset.client;
            const rackSelect = document.querySelector(`.add-file-rack[data-client="${clientId}"]`);
            rackSelect.innerHTML = '<option value="">اختر</option>';
            rackSelect.disabled = true;
            if (this.value) loadAddFileRacks(clientId, this.value);
        });
    });

    // Helper functions
    function updateAddFilePageSelects(clientId, totalPages) {
        const modal = document.querySelector(`#addFileModal_${clientId}`);
        const selects = modal.querySelectorAll('.add-file-page-from, .add-file-page-to');
        selects.forEach(select => {
            select.innerHTML = '<option value="">' + (select.classList.contains('add-file-page-from') ? 'من' : 'إلى') + '</option>';
            for (let i = 1; i <= totalPages; i++) {
                select.innerHTML += `<option value="${i}">${i}</option>`;
            }
        });
    }

    function getAddFileUsedRanges(clientId) {
        const ranges = [];
        const modal = document.querySelector(`#addFileModal_${clientId}`);
        modal.querySelectorAll('.add-file-item-toggle:checked').forEach(toggle => {
            const itemId = toggle.dataset.item;
            const fromSelect = document.querySelector(`.add-file-page-from[data-client="${clientId}"][data-item="${itemId}"]`);
            const toSelect = document.querySelector(`.add-file-page-to[data-client="${clientId}"][data-item="${itemId}"]`);
            if (fromSelect.value && toSelect.value) {
                ranges.push({ itemId, from: parseInt(fromSelect.value), to: parseInt(toSelect.value) });
            }
        });
        return ranges;
    }

    function updateAddFileUsedRanges(clientId) {
        addFileUsedRanges[clientId] = getAddFileUsedRanges(clientId);
    }

    function updateAddFileAvailablePages(clientId) {
        const modal = document.querySelector(`#addFileModal_${clientId}`);
        const completedRanges = getAddFileUsedRanges(clientId);
        modal.querySelectorAll('.add-file-item-toggle').forEach(toggle => {
            const itemId = toggle.dataset.item;
            const fromSelect = document.querySelector(`.add-file-page-from[data-client="${clientId}"][data-item="${itemId}"]`);
            const toSelect = document.querySelector(`.add-file-page-to[data-client="${clientId}"][data-item="${itemId}"]`);
            if (!toggle.checked) return;
            const otherUsedPages = new Set();
            completedRanges.forEach(range => {
                if (range.itemId !== itemId) {
                    for (let i = range.from; i <= range.to; i++) otherUsedPages.add(i);
                }
            });
            const currentFromValue = parseInt(fromSelect.value) || 0;
            const currentToValue = parseInt(toSelect.value) || 0;
            Array.from(fromSelect.options).forEach(option => {
                if (option.value) {
                    const pageNum = parseInt(option.value);
                    option.disabled = otherUsedPages.has(pageNum) && pageNum !== currentFromValue;
                }
            });
            Array.from(toSelect.options).forEach(option => {
                if (option.value) {
                    const pageNum = parseInt(option.value);
                    const usedByOthers = otherUsedPages.has(pageNum) && pageNum !== currentToValue;
                    option.disabled = usedByOthers || (currentFromValue && pageNum < currentFromValue);
                }
            });
        });
    }

    function resetAddFileModal(clientId) {
        const modal = document.querySelector(`#addFileModal_${clientId}`);
        modal.querySelector('form').reset();
        document.querySelector(`.add-file-pdf-placeholder[data-client="${clientId}"]`).classList.remove('d-none');
        document.querySelector(`.add-file-canvas[data-client="${clientId}"]`).classList.add('d-none');
        document.querySelector(`.add-file-pages-info[data-client="${clientId}"]`).textContent = '0 صفحة';
        modal.querySelectorAll('.add-file-page-from, .add-file-page-to').forEach(select => {
            select.innerHTML = '<option value="">' + (select.classList.contains('add-file-page-from') ? 'من' : 'إلى') + '</option>';
            select.disabled = true;
        });
        modal.querySelectorAll('.add-file-item-toggle').forEach(toggle => toggle.checked = false);
        modal.querySelectorAll('.add-file-zone, .add-file-area, .add-file-lane, .add-file-stand, .add-file-rack').forEach(select => {
            select.innerHTML = '<option value="">اختر</option>';
            select.disabled = true;
        });
        modal.querySelectorAll('.accordion-collapse').forEach(collapse => collapse.classList.remove('show'));
        modal.querySelectorAll('.accordion-button').forEach(btn => btn.classList.add('collapsed'));
        addFileUsedRanges[clientId] = [];
    }

    // API calls
    function loadAddFileDistricts(clientId) {
        fetch('{{ route("admin.api.districts") }}').then(r => r.json()).then(data => {
            const select = document.querySelector(`.add-file-district[data-client="${clientId}"]`);
            select.innerHTML = '<option value="">اختر</option>';
            data.forEach(item => select.innerHTML += `<option value="${item.id}">${item.name}</option>`);
        });
    }

    function loadAddFileZones(clientId, districtId) {
        fetch(`/api/zones/${districtId}`).then(r => r.json()).then(data => {
            const select = document.querySelector(`.add-file-zone[data-client="${clientId}"]`);
            select.innerHTML = '<option value="">اختر</option>';
            data.forEach(item => select.innerHTML += `<option value="${item.id}">${item.name}</option>`);
            select.disabled = false;
        });
    }

    function loadAddFileAreas(clientId, zoneId) {
        fetch(`/api/areas/${zoneId}`).then(r => r.json()).then(data => {
            const select = document.querySelector(`.add-file-area[data-client="${clientId}"]`);
            select.innerHTML = '<option value="">اختر</option>';
            data.forEach(item => select.innerHTML += `<option value="${item.id}">${item.name}</option>`);
            select.disabled = false;
        });
    }

    function loadAddFileRooms(clientId) {
        fetch('{{ route("admin.api.rooms") }}').then(r => r.json()).then(data => {
            const select = document.querySelector(`.add-file-room[data-client="${clientId}"]`);
            select.innerHTML = '<option value="">اختر</option>';
            data.forEach(item => select.innerHTML += `<option value="${item.id}">${item.building_name} - ${item.name}</option>`);
        });
    }

    function loadAddFileLanes(clientId, roomId) {
        fetch(`/api/lanes/${roomId}`).then(r => r.json()).then(data => {
            const select = document.querySelector(`.add-file-lane[data-client="${clientId}"]`);
            select.innerHTML = '<option value="">اختر</option>';
            data.forEach(item => select.innerHTML += `<option value="${item.id}">${item.name}</option>`);
            select.disabled = false;
        });
    }

    function loadAddFileStands(clientId, laneId) {
        fetch(`/api/stands/${laneId}`).then(r => r.json()).then(data => {
            const select = document.querySelector(`.add-file-stand[data-client="${clientId}"]`);
            select.innerHTML = '<option value="">اختر</option>';
            data.forEach(item => select.innerHTML += `<option value="${item.id}">${item.name}</option>`);
            select.disabled = false;
        });
    }

    function loadAddFileRacks(clientId, standId) {
        fetch(`/api/racks/${standId}`).then(r => r.json()).then(data => {
            const select = document.querySelector(`.add-file-rack[data-client="${clientId}"]`);
            select.innerHTML = '<option value="">اختر</option>';
            data.forEach(item => select.innerHTML += `<option value="${item.id}">${item.name}</option>`);
            select.disabled = false;
        });
    }

    // Rotation functionality
    const addFileRotationState = {};

    document.querySelectorAll('.add-file-rotate-left').forEach(btn => {
        btn.addEventListener('click', function() {
            const clientId = this.dataset.client;
            addFileRotationState[clientId] = (addFileRotationState[clientId] || 0) - 90;
            applyAddFileRotation(clientId);
        });
    });

    document.querySelectorAll('.add-file-rotate-right').forEach(btn => {
        btn.addEventListener('click', function() {
            const clientId = this.dataset.client;
            addFileRotationState[clientId] = (addFileRotationState[clientId] || 0) + 90;
            applyAddFileRotation(clientId);
        });
    });

    document.querySelectorAll('.add-file-rotate-reset').forEach(btn => {
        btn.addEventListener('click', function() {
            const clientId = this.dataset.client;
            addFileRotationState[clientId] = 0;
            applyAddFileRotation(clientId);
        });
    });

    function applyAddFileRotation(clientId) {
        const canvas = document.querySelector(`.add-file-canvas[data-client="${clientId}"]`);
        const rotationInput = document.querySelector(`.add-file-rotation[data-client="${clientId}"]`);
        const rotation = addFileRotationState[clientId] || 0;

        canvas.style.transform = `rotate(${rotation}deg)`;
        rotationInput.value = rotation;
    }

    // Reset rotation when modal closes
    document.querySelectorAll('[id^="addFileModal_"]').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function() {
            const clientId = this.id.replace('addFileModal_', '');
            addFileRotationState[clientId] = 0;
            const canvas = document.querySelector(`.add-file-canvas[data-client="${clientId}"]`);
            const rotationInput = document.querySelector(`.add-file-rotation[data-client="${clientId}"]`);
            const rotationControls = document.querySelector(`.add-file-rotation-controls[data-client="${clientId}"]`);
            if (canvas) canvas.style.transform = '';
            if (rotationInput) rotationInput.value = '0';
            if (rotationControls) rotationControls.classList.add('d-none');
        });
    });
});
</script>
