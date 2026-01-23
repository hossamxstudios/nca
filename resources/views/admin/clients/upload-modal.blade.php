@foreach($client->files as $file)
<div class="modal fade" id="uploadFileModal_{{ $file->id }}" tabindex="-1" aria-labelledby="uploadFileModalLabel_{{ $file->id }}" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen modal-dialog-centered scroll-y">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadFileModalLabel_{{ $file->id }}">
                    <i class="ti ti-upload me-2"></i>رفع ملف
                    <span class="badge bg-primary ms-3">{{ $file->file_name }}</span>
                    <span class="badge bg-info ms-2" id="totalPagesInfo_{{ $file->id }}">0 صفحة</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form action="{{ route('admin.files.upload', $file->id) }}" method="POST" enctype="multipart/form-data" class="overflow-hidden upload-file-form d-flex flex-column flex-grow-1" data-file-id="{{ $file->id }}">
                @csrf
                <div class="overflow-auto p-3 modal-body">
                    <div class="row">
                        {{-- Left Side - PDF Preview --}}
                        <div class="col-md-5">
                            <div class="p-3 rounded border pdf-preview-container d-flex flex-column align-items-center justify-content-center bg-light" style="position: sticky; top: 0; height: calc(100vh - 50px);">
                                <div id="pdfPreview_{{ $file->id }}" class="text-center pdf-preview w-100">
                                    <i class="ti ti-file-type-pdf text-muted" style="font-size: 5rem;"></i>
                                    <p class="mt-3 text-muted">معاينة الصفحة الأولى ستظهر هنا</p>
                                </div>
                                <canvas id="pdfCanvas_{{ $file->id }}" class="d-none w-100" style=""></canvas>
                            </div>
                        </div>

                        {{-- Right Side - Form --}}
                        <div class="col-md-7">
                            {{-- File Input --}}
                            <div class="flex-shrink-0 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="ti ti-upload me-1"></i>اختر ملف PDF
                                </label>
                                <input type="file" class="form-control pdf-file-input" name="pdf_file" accept=".pdf" required data-file-id="{{ $file->id }}">
                            </div>

                            {{-- Items Table - 2 Columns --}}
                            @php
                                $rightColumnCount = 37;
                                $rightColumnItems = $items->slice(0, $rightColumnCount);
                                $leftColumnItems = $items->slice($rightColumnCount);
                                $maxRows = max($rightColumnItems->count(), $leftColumnItems->count());
                            @endphp
                            <div class="rounded border">
                                <table class="table mb-0 align-middle table-sm table-bordered" style="--bs-table-cell-padding-y: 0.15rem;">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th style="width: 75px;">إلى</th>
                                            <th style="width: 75px;">من</th>
                                            <th>توصيف المستند</th>
                                            <th style="width: 35px;"></th>
                                            <th style="width: 75px;">إلى</th>
                                            <th style="width: 75px;">من</th>
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
                                        <tr style="height: 24px;">
                                            {{-- Left Column (appears on right in RTL) --}}
                                            @if($leftItem)
                                            <td class="py-0">
                                                <select class="form-select form-select-sm page-to-select"
                                                        name="items[{{ $leftItem->id }}][to_page]"
                                                        id="pageTo_{{ $file->id }}_{{ $leftItem->id }}"
                                                        data-file-id="{{ $file->id }}"
                                                        data-item-id="{{ $leftItem->id }}"
                                                        disabled>
                                                    <option value="">إلى</option>
                                                </select>
                                            </td>
                                            <td class="py-0">
                                                <select class="form-select form-select-sm page-from-select" name="items[{{ $leftItem->id }}][from_page]" id="pageFrom_{{ $file->id }}_{{ $leftItem->id }}" data-file-id="{{ $file->id }}" data-item-id="{{ $leftItem->id }}" disabled>
                                                    <option value="">من</option>
                                                </select>
                                            </td>
                                            <td class="py-0">
                                                <label for="itemToggle_{{ $file->id }}_{{ $leftItem->id }}" class="mb-0 cursor-pointer">
                                                    {{ $leftItem->name }}
                                                </label>
                                            </td>
                                            <td class="py-0">
                                                <div class="form-check form-switch fs-xxl">
                                                    <input class="form-check-input item-toggle" type="checkbox" name="items[{{ $leftItem->id }}][enabled]" id="itemToggle_{{ $file->id }}_{{ $leftItem->id }}" data-file-id="{{ $file->id }}" data-item-id="{{ $leftItem->id }}">
                                                </div>
                                            </td>
                                            @else
                                            <td colspan="4"></td>
                                            @endif
                                            {{-- Right Column (appears on left in RTL) --}}
                                            @if($rightItem)
                                            <td class="py-0">
                                                <select class="form-select form-select-sm page-to-select" name="items[{{ $rightItem->id }}][to_page]" id="pageTo_{{ $file->id }}_{{ $rightItem->id }}" data-file-id="{{ $file->id }}" data-item-id="{{ $rightItem->id }}" disabled>
                                                    <option value="">إلى</option>
                                                </select>
                                            </td>
                                            <td class="py-0">
                                                <select class="form-select form-select-sm page-from-select" name="items[{{ $rightItem->id }}][from_page]" id="pageFrom_{{ $file->id }}_{{ $rightItem->id }}" data-file-id="{{ $file->id }}" data-item-id="{{ $rightItem->id }}" disabled>
                                                    <option value="">من</option>
                                                </select>
                                            </td>
                                            <td class="py-0">
                                                <label for="itemToggle_{{ $file->id }}_{{ $rightItem->id }}" class="mb-0 cursor-pointer">
                                                    {{ $rightItem->name }}
                                                </label>
                                            </td>
                                            <td class="py-0">
                                                <div class="form-check form-switch fs-xxl">
                                                    <input class="form-check-input item-toggle" type="checkbox" name="items[{{ $rightItem->id }}][enabled]" id="itemToggle_{{ $file->id }}_{{ $rightItem->id }}" data-file-id="{{ $file->id }}" data-item-id="{{ $rightItem->id }}">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>إلغاء
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-upload me-1"></i>رفع ومعالجة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<style>
.cursor-pointer { cursor: pointer; }
.pdf-preview-container { min-height: 400px; }
.page-from-select, .page-to-select {
    min-width: 70px !important;
    text-align: center;
    padding-right: 24px !important;
    padding-left: 8px !important;
}
.page-from-select:disabled, .page-to-select:disabled {
    background-color: #e9ecef;
    opacity: 0.5;
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set PDF.js worker
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    // Store used page ranges per file
    const usedRanges = {};

    // Handle PDF file input change
    document.querySelectorAll('.pdf-file-input').forEach(input => {
        input.addEventListener('change', function(e) {
            const fileId = this.dataset.fileId;
            const file = e.target.files[0];

            if (file && file.type === 'application/pdf') {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const typedArray = new Uint8Array(e.target.result);

                    pdfjsLib.getDocument(typedArray).promise.then(function(pdf) {
                        const totalPages = pdf.numPages;

                        // Update total pages display
                        document.getElementById('totalPagesInfo_' + fileId).textContent = totalPages + ' صفحة';

                        // Store total pages for this file
                        document.querySelector(`#uploadFileModal_${fileId}`).dataset.totalPages = totalPages;

                        // Initialize used ranges for this file
                        usedRanges[fileId] = [];

                        // Update all page selects with page numbers
                        updatePageSelects(fileId, totalPages);

                        // Render first page preview
                        pdf.getPage(1).then(function(page) {
                            const canvas = document.getElementById('pdfCanvas_' + fileId);
                            const context = canvas.getContext('2d');
                            const viewport = page.getViewport({ scale: 1.0 });

                            // Scale to fit container
                            const containerWidth = canvas.parentElement.offsetWidth - 40;
                            const scale = containerWidth / viewport.width;
                            const scaledViewport = page.getViewport({ scale: scale });

                            canvas.height = scaledViewport.height;
                            canvas.width = scaledViewport.width;

                            page.render({
                                canvasContext: context,
                                viewport: scaledViewport
                            });

                            // Show canvas, hide placeholder
                            canvas.classList.remove('d-none');
                            document.getElementById('pdfPreview_' + fileId).classList.add('d-none');
                        });
                    });
                };
                reader.readAsArrayBuffer(file);
            }
        });
    });

    // Handle item toggle
    document.querySelectorAll('.item-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const fileId = this.dataset.fileId;
            const itemId = this.dataset.itemId;
            const fromSelect = document.getElementById('pageFrom_' + fileId + '_' + itemId);
            const toSelect = document.getElementById('pageTo_' + fileId + '_' + itemId);

            if (this.checked) {
                fromSelect.disabled = false;
                toSelect.disabled = false;
                fromSelect.required = true;
                toSelect.required = true;
                // Update available pages to lock already used ranges
                updateAvailablePages(fileId);
            } else {
                fromSelect.disabled = true;
                toSelect.disabled = true;
                fromSelect.required = false;
                toSelect.required = false;
                fromSelect.value = '';
                toSelect.value = '';

                // Remove this item's range from used ranges and update all
                updateUsedRanges(fileId);
                updateAvailablePages(fileId);
            }
        });
    });

    // Handle page range selection
    document.querySelectorAll('.page-from-select').forEach(select => {
        select.addEventListener('change', function() {
            const fileId = this.dataset.fileId;
            const itemId = this.dataset.itemId;
            const toSelect = document.getElementById('pageTo_' + fileId + '_' + itemId);

            // Auto-set 'to' to same value as 'from' if 'to' is empty or less than 'from'
            if (this.value && (!toSelect.value || parseInt(toSelect.value) < parseInt(this.value))) {
                toSelect.value = this.value;
            }

            updateUsedRanges(fileId);
            updateAvailablePages(fileId);
        });
    });

    document.querySelectorAll('.page-to-select').forEach(select => {
        select.addEventListener('change', function() {
            const fileId = this.dataset.fileId;
            updateUsedRanges(fileId);
            updateAvailablePages(fileId);
        });
    });

    // Update page select options
    function updatePageSelects(fileId, totalPages) {
        const modal = document.querySelector(`#uploadFileModal_${fileId}`);
        const selects = modal.querySelectorAll('.page-from-select, .page-to-select');

        selects.forEach(select => {
            const currentValue = select.value;
            select.innerHTML = '<option value="">' + (select.classList.contains('page-from-select') ? 'من' : 'إلى') + '</option>';

            for (let i = 1; i <= totalPages; i++) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = i;
                select.appendChild(option);
            }

            if (currentValue) {
                select.value = currentValue;
            }
        });
    }

    // Get all used page ranges from completed selections
    function getUsedRanges(fileId) {
        const ranges = [];
        const modal = document.querySelector(`#uploadFileModal_${fileId}`);
        const toggles = modal.querySelectorAll('.item-toggle:checked');

        toggles.forEach(toggle => {
            const itemId = toggle.dataset.itemId;
            const fromSelect = document.getElementById('pageFrom_' + fileId + '_' + itemId);
            const toSelect = document.getElementById('pageTo_' + fileId + '_' + itemId);

            // Only add to used ranges if BOTH from and to are selected
            if (fromSelect.value && toSelect.value) {
                ranges.push({
                    itemId: itemId,
                    from: parseInt(fromSelect.value),
                    to: parseInt(toSelect.value)
                });
            }
        });
        return ranges;
    }

    // Update used page ranges
    function updateUsedRanges(fileId) {
        usedRanges[fileId] = getUsedRanges(fileId);
    }

    // Update available pages (disable pages used by other items)
    function updateAvailablePages(fileId) {
        const modal = document.querySelector(`#uploadFileModal_${fileId}`);
        const toggles = modal.querySelectorAll('.item-toggle');

        // Get all completed ranges
        const completedRanges = getUsedRanges(fileId);

        toggles.forEach(toggle => {
            const itemId = toggle.dataset.itemId;
            const fromSelect = document.getElementById('pageFrom_' + fileId + '_' + itemId);
            const toSelect = document.getElementById('pageTo_' + fileId + '_' + itemId);

            if (!toggle.checked) return;

            // Get pages used by OTHER items (only from completed selections)
            const otherUsedPages = new Set();
            completedRanges.forEach(range => {
                if (range.itemId !== itemId) {
                    for (let i = range.from; i <= range.to; i++) {
                        otherUsedPages.add(i);
                    }
                }
            });

            // Get current selected values
            const currentFromValue = parseInt(fromSelect.value) || 0;
            const currentToValue = parseInt(toSelect.value) || 0;

            // Update from select options - disable pages used by others (but not current selection)
            Array.from(fromSelect.options).forEach(option => {
                if (option.value) {
                    const pageNum = parseInt(option.value);
                    // Disable if used by others, but keep current selection enabled
                    option.disabled = otherUsedPages.has(pageNum) && pageNum !== currentFromValue;
                }
            });

            // Update to select options
            Array.from(toSelect.options).forEach(option => {
                if (option.value) {
                    const pageNum = parseInt(option.value);
                    // Disable if:
                    // 1. Used by other completed items (but not current selection)
                    // 2. Less than selected from value
                    const usedByOthers = otherUsedPages.has(pageNum) && pageNum !== currentToValue;
                    option.disabled = usedByOthers || (currentFromValue && pageNum < currentFromValue);
                }
            });
        });
    }

    // Reset modal on close
    document.querySelectorAll('[id^="uploadFileModal_"]').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function() {
            const fileId = this.id.replace('uploadFileModal_', '');

            // Reset form
            this.querySelector('form').reset();

            // Reset preview
            document.getElementById('pdfPreview_' + fileId).classList.remove('d-none');
            document.getElementById('pdfCanvas_' + fileId).classList.add('d-none');
            document.getElementById('totalPagesInfo_' + fileId).textContent = '0 صفحة';

            // Reset all selects
            this.querySelectorAll('.page-from-select, .page-to-select').forEach(select => {
                select.innerHTML = '<option value="">' + (select.classList.contains('page-from-select') ? 'من' : 'إلى') + '</option>';
                select.disabled = true;
            });

            // Reset toggles
            this.querySelectorAll('.item-toggle').forEach(toggle => {
                toggle.checked = false;
            });

            // Clear used ranges
            usedRanges[fileId] = [];
        });
    });
});
</script>
