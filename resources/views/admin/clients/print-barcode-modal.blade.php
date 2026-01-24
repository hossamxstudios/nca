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
                <div class="mt-4 text-center">
                    <small class="text-muted">
                        <i class="ti ti-info-circle me-1"></i>
                        سيتم طباعة استيكر لكل صفحة في الملف
                    </small>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>إغلاق
                </button>
                <button type="button" class="btn btn-primary" id="btnPrintAll">
                    <i class="ti ti-printer me-1"></i>طباعة الكل
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
        if ($firstFileWithBarcode && $firstFileWithBarcode->land) {
            $geoLocation = collect([
                $firstFileWithBarcode->land?->district?->name,
                $firstFileWithBarcode->land?->zone?->name,
                $firstFileWithBarcode->land?->area?->name,
                $firstFileWithBarcode->land?->land_no
            ])->filter()->implode(' - ') ?: '-';
        }
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
                        </div>
                        <div class="mb-3 d-flex justify-content-center">
                            <label class="form-label text-muted">معاينة الاستيكر (38×25 مم)</label>
                        </div>
                        <div class="d-flex justify-content-center">
                            <div class="barcode-sticker-single">
                                <div class="sticker-client-name">{{ $client->name }}</div>
                                <div class="sticker-geo">{{ $geoLocation }}</div>
                                <svg class="barcode-svg" data-barcode="{{ $firstFileWithBarcode->barcode }}"></svg>
                                <div class="sticker-barcode-text">{{ $firstFileWithBarcode->barcode }}</div>
                                <div class="sticker-file-name">{{ $client->files->count() }} ملف ({{ $totalPages }} صفحة)</div>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <small class="text-muted">
                                <i class="ti ti-info-circle me-1"></i>
                                سيتم طباعة {{ $totalPages }} استيكر (نسخة لكل صفحة)
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                            <i class="ti ti-x me-1"></i>إغلاق
                        </button>
                        <button type="button" class="btn btn-primary btn-print-single"
                                data-client-id="{{ $client->id }}"
                                data-client="{{ $client->name }}"
                                data-geo="{{ $geoLocation }}"
                                data-barcode="{{ $firstFileWithBarcode->barcode }}"
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
                            width: 1.2,
                            height: 35,
                            displayValue: false,
                            margin: 0
                        });
                    }
                });
            });
        });
        // Print single file
        document.querySelectorAll('.btn-print-single').forEach(btn => {
            btn.addEventListener('click', function() {
                const clientId = this.dataset.clientId;
                const clientName = this.dataset.client;
                const geo = this.dataset.geo;
                const barcode = this.dataset.barcode;
                const pagesCount = parseInt(this.dataset.pages) || 1;
                const svgElement = this.closest('.modal').querySelector('.barcode-svg');
                const barcodeSvg = svgElement ? svgElement.outerHTML : '';

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
                        pages_count: pagesCount
                    })
                });

                let stickersHtml = '';
                for (let i = 0; i < pagesCount; i++) {
                    stickersHtml += `
                        <div class="sticker">
                            <div class="client-name">${clientName}</div>
                            <div class="geo">${geo}</div>
                            <div class="barcode">${barcodeSvg}</div>
                            <div class="barcode-text">${barcode}</div>
                        </div>
                    `;
                }
                openPrintWindow(stickersHtml, clientName);
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
                document.getElementById('filesCount').textContent = `${currentFilesData.length} ملف - ${totalStickers} استيكر`;
                const previewContainer = document.getElementById('stickersPreview');
                previewContainer.innerHTML = '';
                currentFilesData.forEach((file, index) => {
                    const stickerDiv = document.createElement('div');
                    stickerDiv.className = 'barcode-sticker';
                    stickerDiv.innerHTML = `
                        <div class="sticker-client-name">${currentClientName}</div>
                        <div class="sticker-geo">${file.geo || '-'}</div>
                        <svg id="barcodeSvg_multi_${index}"></svg>
                        <div class="sticker-barcode-text">${file.barcode}</div>
                        <div class="sticker-file-name">${file.file_name} (${file.pages_count || 1} صفحة)</div>
                    `;
                    previewContainer.appendChild(stickerDiv);
                    setTimeout(() => {
                        JsBarcode(`#barcodeSvg_multi_${index}`, file.barcode, {
                            format: 'CODE128',
                            width: 1.2,
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
            let stickersHtml = '';
            currentFilesData.forEach((file, index) => {
                const svgElement = document.getElementById(`barcodeSvg_multi_${index}`);
                const barcodeSvg = svgElement ? svgElement.outerHTML : '';
                const copies = file.pages_count || 1;
                for (let i = 0; i < copies; i++) {
                    stickersHtml += `
                        <div class="sticker">
                            <div class="client-name">${currentClientName}</div>
                            <div class="geo">${file.geo || '-'}</div>
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
                            font-size: 6pt; font-weight: bold; text-align: center;
                            max-width: 36mm; white-space: nowrap;
                            overflow: hidden; text-overflow: ellipsis; line-height: 1.1;
                        }
                        .geo {
                            font-size: 4pt; text-align: center; color: #333;
                            max-width: 36mm; white-space: nowrap;
                            overflow: hidden; text-overflow: ellipsis; line-height: 1.1;
                        }
                        .barcode { display: flex; justify-content: center; }
                        .barcode svg { max-width: 34mm; height: 12mm; }
                        .barcode-text { font-size: 5pt; font-family: monospace; text-align: center; line-height: 1.1; }
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
