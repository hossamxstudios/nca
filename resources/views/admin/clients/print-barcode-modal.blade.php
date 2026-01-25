{{-- Print Barcode Modal for Index Page (Multiple Clients) --}}
<div class="modal fade" id="printBarcodeModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-printer me-2"></i>طباعة الباركود
                    <span class="badge bg-primary ms-2" id="filesCount"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 text-center">
                    <h6 class="mb-1 text-muted">العميل</h6>
                    <strong id="printClientName"></strong>
                </div>
                <div class="mb-3">
                    <label class="text-center form-label text-muted d-block">معاينة الاستيكرات (38×25 مم)</label>
                </div>
                <div id="stickersPreview" class="flex-wrap gap-3 d-flex justify-content-center"></div>
                <div class="mt-4">
                    <label class="mb-2 text-center form-label text-muted d-block">
                        <i class="ti ti-settings me-1"></i>خيارات الطباعة
                    </label>
                    <div class="gap-3 d-flex justify-content-center">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="bulkPrintOption" id="bulkPrintAll" value="all" checked>
                            <label class="form-check-label" for="bulkPrintAll">
                                كل الصفحات <span id="totalStickersCount" class="badge bg-primary"></span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="bulkPrintOption" id="bulkPrintFirst" value="first">
                            <label class="form-check-label" for="bulkPrintFirst">
                                أول صفحة لكل ملف <span id="firstPageStickersCount" class="badge bg-secondary"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>إغلاق
                </button>
                <button type="button" class="btn btn-primary" id="btnPrintAll">
                    <i class="ti ti-printer me-1"></i>طباعة
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Individual Print Modal per CLIENT (single barcode with total pages) --}}
@foreach($clients as $client)
    @php
        $firstFileWithBarcode = $client->files->first(fn($f) => $f->barcode);
        $totalPages = $client->files->sum('pages_count') ?: 1;
        $geoLocation = '-';
        $physicalLocation = '-';
        if ($firstFileWithBarcode) {
            if ($firstFileWithBarcode->land) {
                $geoLocation = collect([
                    $firstFileWithBarcode->land?->district?->name,
                    $firstFileWithBarcode->land?->zone?->name,
                    $firstFileWithBarcode->land?->area?->name,
                    $firstFileWithBarcode->land?->land_no ? 'أرض ' . $firstFileWithBarcode->land->land_no : null
                ])->filter()->implode(' - ') ?: '-';
            }
            $physicalLocation = collect([
                $firstFileWithBarcode->room?->name ? 'غرفة' . $firstFileWithBarcode->room->name : null,
                $firstFileWithBarcode->lane?->name ? 'ممر' . $firstFileWithBarcode->lane->name : null,
                $firstFileWithBarcode->stand?->name ? 'ستاند' . $firstFileWithBarcode->stand->name : null,
                $firstFileWithBarcode->rack?->name ? 'رف' . $firstFileWithBarcode->rack->name : null
            ])->filter()->implode(' - ') ?: '-';
        }
    @endphp
    @php
        // Prepare all files data for this client
        $clientFilesData = $client->files->filter(fn($f) => $f->barcode)->map(function($f) use ($client) {
            $fileGeo = $f->land ? collect([
                $f->land?->district?->name,
                $f->land?->zone?->name,
                $f->land?->area?->name,
                $f->land?->land_no ? 'أرض ' . $f->land->land_no : null
            ])->filter()->implode(' - ') : '-';
            $filePhysical = collect([
                $f->room?->name ? 'غرفة ' . $f->room->name : null,
                $f->lane?->name ? 'ممر ' . $f->lane->name : null,
                $f->stand?->name ? 'ستاند ' . $f->stand->name : null,
                $f->rack?->name ? 'رف ' . $f->rack->name : null,
            ])->filter()->implode(' - ') ?: '-';
            return [
                'barcode' => $f->barcode,
                'file_name' => $f->file_name,
                'pages_count' => $f->pages_count ?? 1,
                'geo' => $fileGeo,
                'physical' => $filePhysical,
            ];
        })->values();
    @endphp
    @if($firstFileWithBarcode)
        <div class="modal fade" id="printBarcodeModal_{{ $client->id }}" tabindex="-1">
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
                            <h6 class="mb-1 text-muted">العميل</h6>
                            <strong>{{ $client->name }}</strong>
                            <span class="badge bg-secondary ms-2">{{ $clientFilesData->count() }} ملف</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-center">
                            <label class="form-label text-muted">معاينة الاستيكر (38×25 مم)</label>
                        </div>
                        <div class="d-flex justify-content-center">
                            <div class="barcode-sticker-single">
                                <div class="sticker-client-name">{{ $client->name }}</div>
                                <div class="sticker-geo">{{ $geoLocation }}</div>
                                <div class="sticker-physical">{{ $physicalLocation }}</div>
                                <svg class="barcode-svg" data-barcode="{{ $firstFileWithBarcode->barcode }}"></svg>
                                <div class="sticker-barcode-text">{{ $firstFileWithBarcode->barcode }}</div>
                                <div class="sticker-file-name">{{ $totalPages }} صفحة</div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="mb-2 text-center form-label text-muted d-block">
                                <i class="ti ti-settings me-1"></i>خيارات الطباعة
                            </label>
                            <div class="gap-3 d-flex justify-content-center">
                                <div class="form-check">
                                    <input class="form-check-input print-option-single" type="radio"
                                           name="printOption_{{ $client->id }}" id="printAll_{{ $client->id }}" value="all" checked>
                                    <label class="form-check-label" for="printAll_{{ $client->id }}">
                                        <strong>{{ $totalPages }}</strong> استيكر (كل الصفحات)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input print-option-single" type="radio"
                                           name="printOption_{{ $client->id }}" id="printFirst_{{ $client->id }}" value="first">
                                    <label class="form-check-label" for="printFirst_{{ $client->id }}">
                                        <strong>{{ $clientFilesData->count() }}</strong> استيكر (أول صفحة لكل ملف)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ti ti-x me-1"></i>إغلاق
                        </button>
                        <button type="button" class="btn btn-primary btn-print-single"
                                data-client-id="{{ $client->id }}"
                                data-client="{{ $client->name }}"
                                data-files='@json($clientFilesData)'
                                data-pages="{{ $totalPages }}">
                            <i class="ti ti-printer me-1"></i>طباعة
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="modal fade" id="printBarcodeModal_{{ $client->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti ti-printer me-2"></i>طباعة الباركود
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="py-5 text-center">
                            <div class="mb-3">
                                <i class="ti ti-file-off text-muted" style="font-size: 64px;"></i>
                            </div>
                            <h5 class="mb-2 text-muted">لا يوجد ملف</h5>
                            <p class="mb-0 text-muted">هذا العميل ليس لديه ملفات بها باركود للطباعة</p>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                            <i class="ti ti-x me-1"></i>إغلاق
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

<style>
    .barcode-sticker, .barcode-sticker-single {
        width: 152px;
        height: 100px;
        border: 1px dashed #ccc;
        padding: 2px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1px;
        background: #fff;
        direction: rtl;
    }
    .barcode-sticker .sticker-client-name,
    .barcode-sticker-single .sticker-client-name {
        font-size: 8px;
        font-weight: bold;
        text-align: center;
        line-height: 1.1;
        width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .barcode-sticker .sticker-geo,
    .barcode-sticker-single .sticker-geo {
        font-size: 6px;
        text-align: center;
        color: #666;
        line-height: 1.1;
        width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .barcode-sticker .sticker-physical,
    .barcode-sticker-single .sticker-physical {
        font-size: 5px;
        text-align: center;
        color: #888;
        line-height: 1.1;
        width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .barcode-sticker svg,
    .barcode-sticker-single svg {
        max-width: 140px;
        height: 45px;
    }
    .barcode-sticker .sticker-barcode-text,
    .barcode-sticker-single .sticker-barcode-text {
        font-size: 7px;
        font-family: monospace;
        text-align: center;
        line-height: 1.1;
    }
    .barcode-sticker .sticker-file-name,
    .barcode-sticker-single .sticker-file-name {
        font-size: 5px;
        color: #999;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 100%;
    }
</style>

<script src="{{ asset('dashboard/assets/js/barcode.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Generate barcodes when individual modals open
        document.querySelectorAll('[id^="printBarcodeModal_"]').forEach(modal => {
            modal.addEventListener('shown.bs.modal', function() {
                this.querySelectorAll('.barcode-svg').forEach(svg => {
                    if (!svg.hasChildNodes()) {
                        JsBarcode(svg, svg.dataset.barcode, {
                            format: 'CODE128',
                            width: 1,
                            height: 35,
                            displayValue: false,
                            margin: 0
                        });
                    }
                });
            });
        });
        // Print single client (with all files data)
        document.querySelectorAll('.btn-print-single').forEach(btn => {
            btn.addEventListener('click', function() {
                const clientId = this.dataset.clientId;
                const clientName = this.dataset.client;
                const filesData = JSON.parse(this.dataset.files || '[]');
                const totalPages = parseInt(this.dataset.pages) || 1;

                // Get selected print option
                const printOption = this.closest('.modal').querySelector(`input[name="printOption_${clientId}"]:checked`)?.value || 'all';

                // Create temp container for barcodes
                const tempContainer = document.createElement('div');
                tempContainer.style.position = 'absolute';
                tempContainer.style.left = '-9999px';
                document.body.appendChild(tempContainer);

                // Generate barcodes for all files
                filesData.forEach((file, index) => {
                    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                    svg.id = `singleClientBarcode_${index}`;
                    tempContainer.appendChild(svg);
                    JsBarcode(`#singleClientBarcode_${index}`, file.barcode, {
                        format: "CODE128",
                        width: 1,
                        height: 35,
                        displayValue: false,
                        margin: 0
                    });
                });

                setTimeout(() => {
                    let stickersHtml = '';
                    let actualCount = 0;

                    filesData.forEach((file, index) => {
                        const svgElement = document.getElementById(`singleClientBarcode_${index}`);
                        const barcodeSvg = svgElement ? svgElement.outerHTML : '';
                        const copies = printOption === 'first' ? 1 : (file.pages_count || 1);
                        actualCount += copies;

                        for (let i = 0; i < copies; i++) {
                            stickersHtml += `
                                <div class="sticker">
                                    <div class="client-name">${clientName}</div>
                                    <div class="geo">${file.geo || '-'}</div>
                                    <div class="physical">${file.physical || '-'}</div>
                                    <div class="barcode">${barcodeSvg}</div>
                                    <div class="barcode-text">${file.barcode}</div>
                                </div>
                            `;
                        }
                    });

                    document.body.removeChild(tempContainer);

                    // Log print activity
                    fetch('{{ route("admin.clients.log-print") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            client_id: clientId,
                            client_name: clientName,
                            pages_count: actualCount
                        })
                    });

                    openPrintWindow(stickersHtml, clientName);
                }, 100);
            });
        });
        // Multi-file print modal (from table)
        let currentFilesData = [];
        let currentClientName = '';
        const printModal = document.getElementById('printBarcodeModal');
        if (printModal) {
            printModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                currentClientName = button.getAttribute('data-client-name');
                currentFilesData = JSON.parse(button.getAttribute('data-files') || '[]');
                document.getElementById('printClientName').textContent = currentClientName;
                const totalStickers = currentFilesData.reduce((sum, f) => sum + (f.pages_count || 1), 0);
                const filesCount = currentFilesData.length;
                document.getElementById('filesCount').textContent = `${filesCount} ملف`;
                document.getElementById('totalStickersCount').textContent = totalStickers;
                document.getElementById('firstPageStickersCount').textContent = filesCount;
                // Reset to default option
                document.getElementById('bulkPrintAll').checked = true;
                const previewContainer = document.getElementById('stickersPreview');
                previewContainer.innerHTML = '';
                currentFilesData.forEach((file, index) => {
                    const stickerDiv = document.createElement('div');
                    stickerDiv.className = 'barcode-sticker';
                    stickerDiv.innerHTML = `
                        <div class="sticker-client-name">${currentClientName}</div>
                        <div class="sticker-geo">${file.geo || '-'}</div>
                        <div class="sticker-physical">${file.physical || '-'}</div>
                        <svg id="barcodeSvg_multi_${index}"></svg>
                        <div class="sticker-barcode-text">${file.barcode}</div>
                        <div class="sticker-file-name">${file.file_name} (${file.pages_count || 1} صفحة)</div>
                    `;
                    previewContainer.appendChild(stickerDiv);
                    setTimeout(() => {
                        JsBarcode(`#barcodeSvg_multi_${index}`, file.barcode, {
                            format: 'CODE128',
                            width: 1,
                            height: 35,
                            displayValue: false,
                            margin: 0
                        });
                    }, 50);
                });
            });
        }

        // Print all button
        document.getElementById('btnPrintAll')?.addEventListener('click', function() {
            // Get selected print option
            const printOption = document.querySelector('input[name="bulkPrintOption"]:checked')?.value || 'all';

            let stickersHtml = '';
            currentFilesData.forEach((file, index) => {
                const svgElement = document.getElementById(`barcodeSvg_multi_${index}`);
                const barcodeSvg = svgElement ? svgElement.outerHTML : '';
                const copies = printOption === 'first' ? 1 : (file.pages_count || 1);
                for (let i = 0; i < copies; i++) {
                    stickersHtml += `
                        <div class="sticker">
                            <div class="client-name">${currentClientName}</div>
                            <div class="geo">${file.geo || '-'}</div>
                            <div class="physical">${file.physical || '-'}</div>
                            <div class="barcode">${barcodeSvg}</div>
                            <div class="barcode-text">${file.barcode}</div>
                        </div>
                    `;
                }
            });
            openPrintWindow(stickersHtml, currentClientName);
        });

        function openPrintWindow(stickersHtml, clientName) {
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html dir="rtl">
                <head>
                    <title>طباعة الباركود - ${clientName}</title>
                    <style>
                        @page { size: 38mm 25mm; margin: 0; }
                        * { margin: 0; padding: 0; box-sizing: border-box; }
                        body { font-family: Arial, sans-serif; }
                        .sticker {
                            width: 38mm; height: 25mm; padding: 0.5mm;
                            display: flex; flex-direction: column;
                            align-items: center; justify-content: center;
                            gap: 0.3mm; page-break-after: always; overflow: hidden;
                        }
                        .sticker:last-child { page-break-after: auto; }
                        .client-name {
                            font-size: 5.5pt; font-weight: bold; text-align: center;
                            max-width: 36mm; white-space: nowrap;
                            overflow: hidden; text-overflow: ellipsis; line-height: 1.1;
                        }
                        .geo {
                            font-size: 5.5pt; text-align: center; color: black; font-weight: bold;
                            max-width: 36mm; text-overflow: ellipsis; line-height: 1.1;max-height: 7mm;
                            border-bottom: .1mm solid black;
                        }
                        .physical {
                            font-size: 5.5pt; text-align: center; color: black; font-weight: bold;
                            max-width: 36mm;max-height: 7mm;line-height: 1;
                        }
                        .barcode { display: flex; justify-content: center; }
                        .barcode svg { max-width: 33mm; height: 8mm; max-height: 12mm; }
                        .barcode-text { font-size: 5.5pt; font-family: monospace; text-align: center; line-height: 1.1; }
                    </style>
                </head>
                <body>
                    ${stickersHtml}
                    <script>
                        window.onload = function() {
                            window.print();
                            window.onafterprint = function() { window.close(); };
                        };
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }
    });
</script>
