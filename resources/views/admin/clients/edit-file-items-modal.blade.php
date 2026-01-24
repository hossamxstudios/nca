@foreach($client->files as $file)
@if($file->hasMedia('files'))
@php
    $existingFileItems = $file->fileItems->keyBy('item_id');
    $pdfMedia = $file->getFirstMedia('files');
    $pdfUrl = $pdfMedia ? $pdfMedia->getUrl() : null;
@endphp
<div class="modal fade" id="editFileItemsModal_{{ $file->id }}" tabindex="-1" aria-labelledby="editFileItemsModalLabel_{{ $file->id }}" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen modal-dialog-centered scroll-y">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFileItemsModalLabel_{{ $file->id }}">
                    <i class="ti ti-edit me-2"></i>تعديل الملفات الفرعية
                    <span class="badge bg-primary ms-3">{{ $file->file_name }}</span>
                    <span class="badge bg-info ms-2" id="editTotalPagesInfo_{{ $file->id }}">{{ $file->pages_count ?? 0 }} صفحة</span>
                </h5>
                <div class="gap-2 d-flex align-items-center ms-auto me-3">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="toggleCombineSection({{ $file->id }})">
                        <i class="ti ti-file-plus me-1"></i>دمج ملفات
                    </button>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form action="{{ route('admin.files.update-items', $file->id) }}" method="POST" class="overflow-hidden edit-file-items-form d-flex flex-column flex-grow-1" data-file-id="{{ $file->id }}" data-total-pages="{{ $file->pages_count ?? 0 }}" data-pdf-url="{{ $pdfUrl }}">
                @csrf
                @method('PUT')
                <div class="overflow-auto p-3 modal-body">
                    {{-- Combine Files Section (Hidden by default) --}}
                    <div id="combineSection_{{ $file->id }}" class="p-3 mb-3 rounded border bg-light" style="display: none;">
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <h6 class="mb-0"><i class="ti ti-files me-2"></i>دمج ملفات PDF إضافية</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleCombineSection({{ $file->id }})">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <input type="file" id="additionalFiles_{{ $file->id }}" class="form-control"
                                       accept=".pdf" multiple>
                                <small class="text-muted">اختر ملف PDF واحد أو أكثر لدمجها مع الملف الأساسي</small>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary w-100" onclick="combineFiles({{ $file->id }})">
                                    <i class="ti ti-file-plus me-1"></i>دمج الملفات
                                </button>
                            </div>
                        </div>
                        <div id="combineProgress_{{ $file->id }}" class="mt-3" style="display: none;">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                            </div>
                            <small class="text-muted">جاري دمج الملفات...</small>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Left Side - PDF Preview --}}
                        <div class="col-md-5">
                            <div class="p-3 rounded border pdf-preview-container d-flex flex-column align-items-center justify-content-center bg-light" style="position: sticky; top: 0; height: calc(100vh - 50px);">
                                <canvas id="editPdfCanvas_{{ $file->id }}" class="w-100" style=""></canvas>
                            </div>
                        </div>

                        {{-- Right Side - Form --}}
                        <div class="col-md-7">
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
                                            $leftExisting = $leftItem ? $existingFileItems->get($leftItem->id) : null;
                                            $rightExisting = $rightItem ? $existingFileItems->get($rightItem->id) : null;
                                        @endphp
                                        <tr>
                                            {{-- Left Column (appears on right in RTL) --}}
                                            @if($leftItem)
                                            <td class="py-1">
                                                <select class="form-select form-select-sm edit-page-to-select"
                                                        name="items[{{ $leftItem->id }}][to_page]"
                                                        id="editPageTo_{{ $file->id }}_{{ $leftItem->id }}"
                                                        data-file-id="{{ $file->id }}"
                                                        data-item-id="{{ $leftItem->id }}"
                                                        data-initial-value="{{ $leftExisting?->to_page }}"
                                                        {{ $leftExisting ? '' : 'disabled' }}>
                                                    <option value="">إلى</option>
                                                </select>
                                            </td>
                                            <td class="py-1">
                                                <select class="form-select form-select-sm edit-page-from-select"
                                                        name="items[{{ $leftItem->id }}][from_page]"
                                                        id="editPageFrom_{{ $file->id }}_{{ $leftItem->id }}"
                                                        data-file-id="{{ $file->id }}"
                                                        data-item-id="{{ $leftItem->id }}"
                                                        data-initial-value="{{ $leftExisting?->from_page }}"
                                                        {{ $leftExisting ? '' : 'disabled' }}>
                                                    <option value="">من</option>
                                                </select>
                                            </td>
                                            <td class="py-1">
                                                <label for="editItemToggle_{{ $file->id }}_{{ $leftItem->id }}" class="mb-0 cursor-pointer">
                                                    {{ $leftItem->name }}
                                                </label>
                                            </td>
                                            <td class="py-1">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input edit-item-toggle" type="checkbox"
                                                           name="items[{{ $leftItem->id }}][enabled]"
                                                           id="editItemToggle_{{ $file->id }}_{{ $leftItem->id }}"
                                                           data-file-id="{{ $file->id }}"
                                                           data-item-id="{{ $leftItem->id }}"
                                                           {{ $leftExisting ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            @else
                                            <td colspan="4"></td>
                                            @endif

                                            {{-- Right Column (appears on left in RTL) --}}
                                            @if($rightItem)
                                            <td class="py-1">
                                                <select class="form-select form-select-sm edit-page-to-select"
                                                        name="items[{{ $rightItem->id }}][to_page]"
                                                        id="editPageTo_{{ $file->id }}_{{ $rightItem->id }}"
                                                        data-file-id="{{ $file->id }}"
                                                        data-item-id="{{ $rightItem->id }}"
                                                        data-initial-value="{{ $rightExisting?->to_page }}"
                                                        {{ $rightExisting ? '' : 'disabled' }}>
                                                    <option value="">إلى</option>
                                                </select>
                                            </td>
                                            <td class="py-1">
                                                <select class="form-select form-select-sm edit-page-from-select"
                                                        name="items[{{ $rightItem->id }}][from_page]"
                                                        id="editPageFrom_{{ $file->id }}_{{ $rightItem->id }}"
                                                        data-file-id="{{ $file->id }}"
                                                        data-item-id="{{ $rightItem->id }}"
                                                        data-initial-value="{{ $rightExisting?->from_page }}"
                                                        {{ $rightExisting ? '' : 'disabled' }}>
                                                    <option value="">من</option>
                                                </select>
                                            </td>
                                            <td class="py-1">
                                                <label for="editItemToggle_{{ $file->id }}_{{ $rightItem->id }}" class="mb-0 cursor-pointer">
                                                    {{ $rightItem->name }}
                                                </label>
                                            </td>
                                            <td class="py-1">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input edit-item-toggle" type="checkbox"
                                                           name="items[{{ $rightItem->id }}][enabled]"
                                                           id="editItemToggle_{{ $file->id }}_{{ $rightItem->id }}"
                                                           data-file-id="{{ $file->id }}"
                                                           data-item-id="{{ $rightItem->id }}"
                                                           {{ $rightExisting ? 'checked' : '' }}>
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
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-check me-1"></i>حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

<script>
// Toggle combine section visibility
function toggleCombineSection(fileId) {
    const section = document.getElementById('combineSection_' + fileId);
    section.style.display = section.style.display === 'none' ? 'block' : 'none';
}

// Combine files function
function combineFiles(fileId) {
    const fileInput = document.getElementById('additionalFiles_' + fileId);
    const progressDiv = document.getElementById('combineProgress_' + fileId);

    if (!fileInput.files || fileInput.files.length === 0) {
        alert('الرجاء اختيار ملف PDF واحد على الأقل');
        return;
    }

    const formData = new FormData();
    for (let i = 0; i < fileInput.files.length; i++) {
        formData.append('additional_files[]', fileInput.files[i]);
    }

    progressDiv.style.display = 'block';

    fetch(`/files/${fileId}/combine`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        progressDiv.style.display = 'none';

        if (data.success) {
            // Update total pages badge
            const badge = document.getElementById('editTotalPagesInfo_' + fileId);
            badge.textContent = data.new_total_pages + ' صفحة';

            // Update form data attribute
            const form = document.querySelector(`.edit-file-items-form[data-file-id="${fileId}"]`);
            form.dataset.totalPages = data.new_total_pages;
            form.dataset.pdfUrl = data.pdf_url;

            // Reinitialize page selects with new page count
            initializeEditPageSelects(fileId, data.new_total_pages);

            // Reload PDF preview
            loadEditPdfPreview(fileId, data.pdf_url);

            // Hide combine section and clear input
            toggleCombineSection(fileId);
            fileInput.value = '';

            alert(`تم دمج الملفات بنجاح!\nالصفحات الأصلية: ${data.original_pages}\nالإجمالي الجديد: ${data.new_total_pages}`);
        } else {
            alert(data.error || 'حدث خطأ أثناء دمج الملفات');
        }
    })
    .catch(error => {
        progressDiv.style.display = 'none';
        console.error('Error:', error);
        alert('حدث خطأ أثناء دمج الملفات');
    });
}

// Global functions for page selects
function initializeEditPageSelects(fileId, totalPages) {
    const modal = document.getElementById('editFileItemsModal_' + fileId);
    const selects = modal.querySelectorAll('.edit-page-from-select, .edit-page-to-select');

    selects.forEach(select => {
        const initialValue = select.dataset.initialValue;
        select.innerHTML = '<option value="">' + (select.classList.contains('edit-page-from-select') ? 'من' : 'إلى') + '</option>';

        for (let i = 1; i <= totalPages; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = i;
            select.appendChild(option);
        }

        if (initialValue) {
            select.value = initialValue;
        }
    });
}

function loadEditPdfPreview(fileId, pdfUrl) {
    pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
        pdf.getPage(1).then(function(page) {
            const canvas = document.getElementById('editPdfCanvas_' + fileId);
            const context = canvas.getContext('2d');
            const containerWidth = canvas.parentElement.offsetWidth - 40;
            const viewport = page.getViewport({ scale: 1 });
            const scale = containerWidth / viewport.width;
            const scaledViewport = page.getViewport({ scale: scale });

            canvas.height = scaledViewport.height;
            canvas.width = scaledViewport.width;

            page.render({
                canvasContext: context,
                viewport: scaledViewport
            });
        });
    });
}

function getEditUsedRanges(fileId) {
    const ranges = [];
    const modal = document.getElementById('editFileItemsModal_' + fileId);
    const toggles = modal.querySelectorAll('.edit-item-toggle:checked');

    toggles.forEach(toggle => {
        const itemId = toggle.dataset.itemId;
        const fromSelect = document.getElementById('editPageFrom_' + fileId + '_' + itemId);
        const toSelect = document.getElementById('editPageTo_' + fileId + '_' + itemId);

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

function updateEditAvailablePages(fileId) {
    const modal = document.getElementById('editFileItemsModal_' + fileId);
    const toggles = modal.querySelectorAll('.edit-item-toggle');
    const completedRanges = getEditUsedRanges(fileId);

    toggles.forEach(toggle => {
        const itemId = toggle.dataset.itemId;
        const fromSelect = document.getElementById('editPageFrom_' + fileId + '_' + itemId);
        const toSelect = document.getElementById('editPageTo_' + fileId + '_' + itemId);

        if (!toggle.checked) return;

        const otherUsedPages = new Set();
        completedRanges.forEach(range => {
            if (range.itemId !== itemId) {
                for (let i = range.from; i <= range.to; i++) {
                    otherUsedPages.add(i);
                }
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

document.addEventListener('DOMContentLoaded', function() {
    // Initialize edit modals
    document.querySelectorAll('.edit-file-items-form').forEach(form => {
        const fileId = form.dataset.fileId;
        const totalPages = parseInt(form.dataset.totalPages) || 0;
        const pdfUrl = form.dataset.pdfUrl;
        const modal = document.getElementById('editFileItemsModal_' + fileId);

        // Initialize page selects with options
        initializeEditPageSelects(fileId, totalPages);

        // Load PDF preview when modal opens
        modal.addEventListener('shown.bs.modal', function() {
            if (pdfUrl) {
                loadEditPdfPreview(fileId, pdfUrl);
            }
            // Update available pages based on existing selections
            updateEditAvailablePages(fileId);
        });
    });

    // Handle item toggle
    document.querySelectorAll('.edit-item-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const fileId = this.dataset.fileId;
            const itemId = this.dataset.itemId;
            const fromSelect = document.getElementById('editPageFrom_' + fileId + '_' + itemId);
            const toSelect = document.getElementById('editPageTo_' + fileId + '_' + itemId);

            if (this.checked) {
                fromSelect.disabled = false;
                toSelect.disabled = false;
                fromSelect.required = true;
                toSelect.required = true;
                updateEditAvailablePages(fileId);
            } else {
                fromSelect.disabled = true;
                toSelect.disabled = true;
                fromSelect.required = false;
                toSelect.required = false;
                fromSelect.value = '';
                toSelect.value = '';
                updateEditAvailablePages(fileId);
            }
        });
    });

    // Handle page range selection
    document.querySelectorAll('.edit-page-from-select').forEach(select => {
        select.addEventListener('change', function() {
            const fileId = this.dataset.fileId;
            const itemId = this.dataset.itemId;
            const toSelect = document.getElementById('editPageTo_' + fileId + '_' + itemId);

            if (this.value && (!toSelect.value || parseInt(toSelect.value) < parseInt(this.value))) {
                toSelect.value = this.value;
            }

            updateEditAvailablePages(fileId);
        });
    });

    document.querySelectorAll('.edit-page-to-select').forEach(select => {
        select.addEventListener('change', function() {
            const fileId = this.dataset.fileId;
            updateEditAvailablePages(fileId);
        });
    });
});
</script>
