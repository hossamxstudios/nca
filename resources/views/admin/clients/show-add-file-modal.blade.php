{{-- Add File Modal for Show Page (Single Client) --}}
@php
    $rightColumnCount = 37;
    $rightColumnItems = $items->slice(0, $rightColumnCount);
    $leftColumnItems = $items->slice($rightColumnCount);
    $maxRows = max($rightColumnItems->count(), $leftColumnItems->count());
@endphp

<div class="modal fade" id="showAddFileModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle">
                <h5 class="modal-title">
                    <i class="ti ti-file-plus me-2"></i>إضافة ملف جديد
                    <span class="badge bg-primary ms-2">{{ $client->name }}</span>
                    <span class="badge bg-info ms-2" id="showAddFilePagesInfo">0 صفحة</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.files.store', $client->id) }}" method="POST" enctype="multipart/form-data" class="overflow-hidden d-flex flex-column flex-grow-1" id="showAddFileForm">
                @csrf
                <div class="overflow-auto p-3 modal-body">
                    <div class="row">
                        {{-- Left Side - PDF Preview --}}
                        <div class="col-md-4">
                            <div class="p-3 rounded border pdf-preview-container d-flex flex-column align-items-center justify-content-center bg-light" style="position: sticky; top: 0; height: calc(100vh - 180px);">
                                <div class="text-center w-100" id="showAddFilePdfPlaceholder">
                                    <i class="ti ti-file-type-pdf text-muted" style="font-size: 5rem;"></i>
                                    <p class="mt-3 text-muted">معاينة الصفحة الأولى ستظهر هنا</p>
                                </div>
                                <canvas class="d-none w-100" id="showAddFileCanvas"></canvas>
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
                                    <div class="col-md-8">
                                        <label class="form-label fw-semibold">
                                            <i class="ti ti-upload me-1"></i>اختر ملف PDF <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control" name="pdf_file" accept=".pdf" required id="showAddFilePdfInput">
                                    </div>
                                </div>
                            </div>

                            {{-- Location Sections as Accordions --}}
                            <div class="mb-3 accordion" id="showAddFileLocationAccordion">
                                {{-- Geolocation Section --}}
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="py-2 accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#showAddFileGeoCollapse">
                                            <i class="ti ti-map-pin me-2 text-primary"></i>
                                            <span class="fw-bold">العنوان الجغرافي</span>
                                            <span class="badge bg-danger ms-2">مطلوب</span>
                                        </button>
                                    </h2>
                                    <div id="showAddFileGeoCollapse" class="accordion-collapse collapse" data-bs-parent="#showAddFileLocationAccordion">
                                        <div class="py-2 accordion-body">
                                            <div class="row g-2">
                                                <div class="col-md-3">
                                                    <label class="form-label small">الحي <span class="text-danger">*</span></label>
                                                    <select class="form-select form-select-sm" name="district_id" required id="showAddFileDistrict">
                                                        <option value="">اختر</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">المجاورة <span class="text-danger">*</span></label>
                                                    <select class="form-select form-select-sm" name="zone_id" required id="showAddFileZone" disabled>
                                                        <option value="">اختر</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">المنطقة</label>
                                                    <select class="form-select form-select-sm" name="area_id" id="showAddFileArea" disabled>
                                                        <option value="">اختر</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">رقم القطعة <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm" name="land_no" required placeholder="القطعة">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Physical Location Section --}}
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="py-2 accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#showAddFilePhysicalCollapse">
                                            <i class="ti ti-building me-2 text-info"></i>
                                            <span class="fw-bold">الموقع الفعلي</span>
                                            <span class="badge bg-secondary ms-2">اختياري</span>
                                        </button>
                                    </h2>
                                    <div id="showAddFilePhysicalCollapse" class="accordion-collapse collapse" data-bs-parent="#showAddFileLocationAccordion">
                                        <div class="py-2 accordion-body">
                                            <div class="row g-2">
                                                <div class="col-md-3">
                                                    <label class="form-label small">الغرفة</label>
                                                    <select class="form-select form-select-sm" name="room_id" id="showAddFileRoom">
                                                        <option value="">اختر</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">الممر</label>
                                                    <select class="form-select form-select-sm" name="lane_id" id="showAddFileLane" disabled>
                                                        <option value="">اختر</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">الاستاند</label>
                                                    <select class="form-select form-select-sm" name="stand_id" id="showAddFileStand" disabled>
                                                        <option value="">اختر</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">الرف</label>
                                                    <select class="form-select form-select-sm" name="rack_id" id="showAddFileRack" disabled>
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
                                            <td class="py-1">
                                                <select class="form-select form-select-sm show-add-file-page-to" name="items[{{ $leftItem->id }}][to_page]" data-item="{{ $leftItem->id }}" disabled>
                                                    <option value="">إلى</option>
                                                </select>
                                            </td>
                                            <td class="py-1">
                                                <select class="form-select form-select-sm show-add-file-page-from" name="items[{{ $leftItem->id }}][from_page]" data-item="{{ $leftItem->id }}" disabled>
                                                    <option value="">من</option>
                                                </select>
                                            </td>
                                            <td class="py-1">
                                                <label for="showAddFileItem_{{ $leftItem->id }}" class="mb-0 cursor-pointer">{{ $leftItem->name }}</label>
                                            </td>
                                            <td class="py-1">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input show-add-file-item-toggle" type="checkbox" name="items[{{ $leftItem->id }}][enabled]" id="showAddFileItem_{{ $leftItem->id }}" data-item="{{ $leftItem->id }}">
                                                </div>
                                            </td>
                                            @else
                                            <td colspan="4"></td>
                                            @endif

                                            @if($rightItem)
                                            <td class="py-1">
                                                <select class="form-select form-select-sm show-add-file-page-to" name="items[{{ $rightItem->id }}][to_page]" data-item="{{ $rightItem->id }}" disabled>
                                                    <option value="">إلى</option>
                                                </select>
                                            </td>
                                            <td class="py-1">
                                                <select class="form-select form-select-sm show-add-file-page-from" name="items[{{ $rightItem->id }}][from_page]" data-item="{{ $rightItem->id }}" disabled>
                                                    <option value="">من</option>
                                                </select>
                                            </td>
                                            <td class="py-1">
                                                <label for="showAddFileItem_{{ $rightItem->id }}" class="mb-0 cursor-pointer">{{ $rightItem->name }}</label>
                                            </td>
                                            <td class="py-1">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input show-add-file-item-toggle" type="checkbox" name="items[{{ $rightItem->id }}][enabled]" id="showAddFileItem_{{ $rightItem->id }}" data-item="{{ $rightItem->id }}">
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
                        <i class="ti ti-plus me-1"></i>إضافة ورفع الملف
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.show-add-file-page-from, .show-add-file-page-to {
    min-width: 65px !important;
    text-align: center;
    padding-right: 20px !important;
    padding-left: 6px !important;
}
.show-add-file-page-from:disabled, .show-add-file-page-to:disabled {
    background-color: #e9ecef;
    opacity: 0.5;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('showAddFileModal');
    if (!modal) return;

    let totalPages = 0;
    let usedRanges = [];

    // Load districts and rooms on modal show
    modal.addEventListener('show.bs.modal', function() {
        loadDistricts();
        loadRooms();
    });

    modal.addEventListener('hidden.bs.modal', function() {
        resetModal();
    });

    // PDF file input change
    document.getElementById('showAddFilePdfInput')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file && file.type === 'application/pdf') {
            const reader = new FileReader();
            reader.onload = function(e) {
                const typedArray = new Uint8Array(e.target.result);
                pdfjsLib.getDocument(typedArray).promise.then(function(pdf) {
                    totalPages = pdf.numPages;
                    document.getElementById('showAddFilePagesInfo').textContent = totalPages + ' صفحة';
                    usedRanges = [];
                    updatePageSelects(totalPages);

                    pdf.getPage(1).then(function(page) {
                        const canvas = document.getElementById('showAddFileCanvas');
                        const context = canvas.getContext('2d');
                        const viewport = page.getViewport({ scale: 1.0 });
                        const containerWidth = canvas.parentElement.offsetWidth - 40;
                        const scale = containerWidth / viewport.width;
                        const scaledViewport = page.getViewport({ scale: scale });
                        canvas.height = scaledViewport.height;
                        canvas.width = scaledViewport.width;
                        page.render({ canvasContext: context, viewport: scaledViewport });
                        canvas.classList.remove('d-none');
                        document.getElementById('showAddFilePdfPlaceholder').classList.add('d-none');
                    });
                });
            };
            reader.readAsArrayBuffer(file);
        }
    });

    // Item toggle
    document.querySelectorAll('.show-add-file-item-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const itemId = this.dataset.item;
            const fromSelect = document.querySelector(`.show-add-file-page-from[data-item="${itemId}"]`);
            const toSelect = document.querySelector(`.show-add-file-page-to[data-item="${itemId}"]`);
            if (this.checked) {
                fromSelect.disabled = false;
                toSelect.disabled = false;
                fromSelect.required = true;
                toSelect.required = true;
                updateAvailablePages();
            } else {
                fromSelect.disabled = true;
                toSelect.disabled = true;
                fromSelect.required = false;
                toSelect.required = false;
                fromSelect.value = '';
                toSelect.value = '';
                updateUsedRanges();
                updateAvailablePages();
            }
        });
    });

    // Page from/to change
    document.querySelectorAll('.show-add-file-page-from').forEach(select => {
        select.addEventListener('change', function() {
            const itemId = this.dataset.item;
            const toSelect = document.querySelector(`.show-add-file-page-to[data-item="${itemId}"]`);
            if (this.value && (!toSelect.value || parseInt(toSelect.value) < parseInt(this.value))) {
                toSelect.value = this.value;
            }
            updateUsedRanges();
            updateAvailablePages();
        });
    });

    document.querySelectorAll('.show-add-file-page-to').forEach(select => {
        select.addEventListener('change', function() {
            updateUsedRanges();
            updateAvailablePages();
        });
    });

    // Cascading dropdowns - District
    document.getElementById('showAddFileDistrict')?.addEventListener('change', function() {
        const zoneSelect = document.getElementById('showAddFileZone');
        const areaSelect = document.getElementById('showAddFileArea');
        zoneSelect.innerHTML = '<option value="">اختر</option>';
        areaSelect.innerHTML = '<option value="">اختر</option>';
        zoneSelect.disabled = true;
        areaSelect.disabled = true;
        if (this.value) loadZones(this.value);
    });

    // Zone
    document.getElementById('showAddFileZone')?.addEventListener('change', function() {
        const areaSelect = document.getElementById('showAddFileArea');
        areaSelect.innerHTML = '<option value="">اختر</option>';
        areaSelect.disabled = true;
        if (this.value) loadAreas(this.value);
    });

    // Room
    document.getElementById('showAddFileRoom')?.addEventListener('change', function() {
        const laneSelect = document.getElementById('showAddFileLane');
        const standSelect = document.getElementById('showAddFileStand');
        const rackSelect = document.getElementById('showAddFileRack');
        laneSelect.innerHTML = '<option value="">اختر</option>';
        standSelect.innerHTML = '<option value="">اختر</option>';
        rackSelect.innerHTML = '<option value="">اختر</option>';
        laneSelect.disabled = true;
        standSelect.disabled = true;
        rackSelect.disabled = true;
        if (this.value) loadLanes(this.value);
    });

    // Lane
    document.getElementById('showAddFileLane')?.addEventListener('change', function() {
        const standSelect = document.getElementById('showAddFileStand');
        const rackSelect = document.getElementById('showAddFileRack');
        standSelect.innerHTML = '<option value="">اختر</option>';
        rackSelect.innerHTML = '<option value="">اختر</option>';
        standSelect.disabled = true;
        rackSelect.disabled = true;
        if (this.value) loadStands(this.value);
    });

    // Stand
    document.getElementById('showAddFileStand')?.addEventListener('change', function() {
        const rackSelect = document.getElementById('showAddFileRack');
        rackSelect.innerHTML = '<option value="">اختر</option>';
        rackSelect.disabled = true;
        if (this.value) loadRacks(this.value);
    });

    // Helper functions
    function updatePageSelects(pages) {
        document.querySelectorAll('.show-add-file-page-from, .show-add-file-page-to').forEach(select => {
            const isFrom = select.classList.contains('show-add-file-page-from');
            select.innerHTML = `<option value="">${isFrom ? 'من' : 'إلى'}</option>`;
            for (let i = 1; i <= pages; i++) {
                select.innerHTML += `<option value="${i}">${i}</option>`;
            }
        });
    }

    function getUsedRanges() {
        const ranges = [];
        document.querySelectorAll('.show-add-file-item-toggle:checked').forEach(toggle => {
            const itemId = toggle.dataset.item;
            const fromSelect = document.querySelector(`.show-add-file-page-from[data-item="${itemId}"]`);
            const toSelect = document.querySelector(`.show-add-file-page-to[data-item="${itemId}"]`);
            if (fromSelect.value && toSelect.value) {
                ranges.push({ itemId, from: parseInt(fromSelect.value), to: parseInt(toSelect.value) });
            }
        });
        return ranges;
    }

    function updateUsedRanges() {
        usedRanges = getUsedRanges();
    }

    function updateAvailablePages() {
        const completedRanges = getUsedRanges();
        document.querySelectorAll('.show-add-file-item-toggle').forEach(toggle => {
            const itemId = toggle.dataset.item;
            const fromSelect = document.querySelector(`.show-add-file-page-from[data-item="${itemId}"]`);
            const toSelect = document.querySelector(`.show-add-file-page-to[data-item="${itemId}"]`);
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

    function resetModal() {
        document.getElementById('showAddFileForm').reset();
        document.getElementById('showAddFilePdfPlaceholder').classList.remove('d-none');
        document.getElementById('showAddFileCanvas').classList.add('d-none');
        document.getElementById('showAddFilePagesInfo').textContent = '0 صفحة';
        document.querySelectorAll('.show-add-file-page-from, .show-add-file-page-to').forEach(select => {
            const isFrom = select.classList.contains('show-add-file-page-from');
            select.innerHTML = `<option value="">${isFrom ? 'من' : 'إلى'}</option>`;
            select.disabled = true;
        });
        document.querySelectorAll('.show-add-file-item-toggle').forEach(toggle => toggle.checked = false);
        ['showAddFileZone', 'showAddFileArea', 'showAddFileLane', 'showAddFileStand', 'showAddFileRack'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.innerHTML = '<option value="">اختر</option>';
                el.disabled = true;
            }
        });
        document.querySelectorAll('#showAddFileLocationAccordion .accordion-collapse').forEach(collapse => collapse.classList.remove('show'));
        document.querySelectorAll('#showAddFileLocationAccordion .accordion-button').forEach(btn => btn.classList.add('collapsed'));
        totalPages = 0;
        usedRanges = [];
    }

    // API calls
    function loadDistricts() {
        fetch('{{ route("admin.api.districts") }}').then(r => r.json()).then(data => {
            const select = document.getElementById('showAddFileDistrict');
            select.innerHTML = '<option value="">اختر</option>';
            data.forEach(item => select.innerHTML += `<option value="${item.id}">${item.name}</option>`);
        });
    }

    function loadZones(districtId) {
        fetch(`/api/zones/${districtId}`).then(r => r.json()).then(data => {
            const select = document.getElementById('showAddFileZone');
            select.innerHTML = '<option value="">اختر</option>';
            data.forEach(item => select.innerHTML += `<option value="${item.id}">${item.name}</option>`);
            select.disabled = false;
        });
    }

    function loadAreas(zoneId) {
        fetch(`/api/areas/${zoneId}`).then(r => r.json()).then(data => {
            const select = document.getElementById('showAddFileArea');
            select.innerHTML = '<option value="">اختر</option>';
            data.forEach(item => select.innerHTML += `<option value="${item.id}">${item.name}</option>`);
            select.disabled = false;
        });
    }

    function loadRooms() {
        fetch('{{ route("admin.api.rooms") }}').then(r => r.json()).then(data => {
            const select = document.getElementById('showAddFileRoom');
            select.innerHTML = '<option value="">اختر</option>';
            data.forEach(item => select.innerHTML += `<option value="${item.id}">${item.building_name} - ${item.name}</option>`);
        });
    }

    function loadLanes(roomId) {
        fetch(`/api/lanes/${roomId}`).then(r => r.json()).then(data => {
            const select = document.getElementById('showAddFileLane');
            select.innerHTML = '<option value="">اختر</option>';
            data.forEach(item => select.innerHTML += `<option value="${item.id}">${item.name}</option>`);
            select.disabled = false;
        });
    }

    function loadStands(laneId) {
        fetch(`/api/stands/${laneId}`).then(r => r.json()).then(data => {
            const select = document.getElementById('showAddFileStand');
            select.innerHTML = '<option value="">اختر</option>';
            data.forEach(item => select.innerHTML += `<option value="${item.id}">${item.name}</option>`);
            select.disabled = false;
        });
    }

    function loadRacks(standId) {
        fetch(`/api/racks/${standId}`).then(r => r.json()).then(data => {
            const select = document.getElementById('showAddFileRack');
            select.innerHTML = '<option value="">اختر</option>';
            data.forEach(item => select.innerHTML += `<option value="${item.id}">${item.name}</option>`);
            select.disabled = false;
        });
    }
});
</script>
