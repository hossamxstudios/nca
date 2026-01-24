{{-- Individual Print Modals for Each File in Show Page --}}
@foreach($client->files as $file)
    @if($file->barcode)
    @php
        $geoLocation = collect([
            $file->land?->district?->name,
            $file->land?->zone?->name,
            $file->land?->area?->name,
            $file->land?->land_no ? 'أرض ' . $file->land->land_no : null
        ])->filter()->implode(' - ') ?: '-';
        $physicalLocation = collect([
            $file->room?->name ? 'غرفة ' . $file->room->name : null,
            $file->lane?->name ? 'ممر ' . $file->lane->name : null,
            $file->stand?->name ? 'ستاند ' . $file->stand->name : null,
            $file->rack?->name ? 'رف ' . $file->rack->name : null,
        ])->filter()->implode(' - ') ?: '-';
    @endphp
    <div class="modal fade" id="printBarcodeModal_{{ $file->id }}" tabindex="-1" aria-hidden="true">
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
                            <div class="sticker-physical">{{ $physicalLocation }}</div>
                            <svg class="barcode-svg" data-barcode="{{ $file->barcode }}"></svg>
                            <div class="sticker-barcode-text">{{ $file->barcode }}</div>
                            <div class="sticker-file-name">{{ $file->file_name }} ({{ $file->pages_count ?? 1 }} صفحة)</div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="mb-2 text-center form-label text-muted d-block">
                            <i class="ti ti-settings me-1"></i>خيارات الطباعة
                        </label>
                        <div class="gap-3 d-flex justify-content-center">
                            <div class="form-check">
                                <input class="form-check-input print-option-show" type="radio"
                                       name="printOptionShow_{{ $file->id }}" id="printAllShow_{{ $file->id }}" value="all" checked>
                                <label class="form-check-label" for="printAllShow_{{ $file->id }}">
                                    <strong>{{ $file->pages_count ?? 1 }}</strong> استيكر (كل الصفحات)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input print-option-show" type="radio"
                                       name="printOptionShow_{{ $file->id }}" id="printFirstShow_{{ $file->id }}" value="first">
                                <label class="form-check-label" for="printFirstShow_{{ $file->id }}">
                                    <strong>1</strong> استيكر (أول صفحة فقط)
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
                            data-file-id="{{ $file->id }}"
                            data-client="{{ $client->name }}"
                            data-geo="{{ $geoLocation }}"
                            data-physical="{{ $physicalLocation }}"
                            data-barcode="{{ $file->barcode }}"
                            data-pages="{{ $file->pages_count ?? 1 }}">
                        <i class="ti ti-printer me-1"></i>طباعة
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach

<style>
    .barcode-sticker-single {
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
    .barcode-sticker-single svg {
        max-width: 140px;
        height: 45px;
    }
    .barcode-sticker-single .sticker-barcode-text {
        font-size: 7px;
        font-family: monospace;
        text-align: center;
        line-height: 1.1;
    }
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

    // Print single file
    document.querySelectorAll('.btn-print-single').forEach(btn => {
        btn.addEventListener('click', function() {
            const fileId = this.dataset.fileId;
            const clientName = this.dataset.client;
            const geo = this.dataset.geo;
            const physical = this.dataset.physical;
            const barcode = this.dataset.barcode;
            const pagesCount = parseInt(this.dataset.pages) || 1;
            const svgElement = this.closest('.modal').querySelector('.barcode-svg');
            const barcodeSvg = svgElement ? svgElement.outerHTML : '';

            // Get selected print option
            const printOption = this.closest('.modal').querySelector(`input[name="printOptionShow_${fileId}"]:checked`)?.value || 'all';
            const actualCount = printOption === 'first' ? 1 : pagesCount;

            let stickersHtml = '';
            for (let i = 0; i < actualCount; i++) {
                stickersHtml += `
                    <div class="sticker">
                        <div class="client-name">${clientName}</div>
                        <div class="geo">${geo}</div>
                        <div class="physical">${physical}</div>
                        <div class="barcode">${barcodeSvg}</div>
                        <div class="barcode-text">${barcode}</div>
                    </div>
                `;
            }

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
                            font-size: 3pt; font-weight: bold; text-align: center;
                            max-width: 36mm; white-space: nowrap;
                            overflow: hidden; text-overflow: ellipsis; line-height: 1.1;
                        }
                        .geo {
                            font-size: 3pt; text-align: center; color: black; font-weight: bold;
                            max-width: 36mm; text-overflow: ellipsis; line-height: 1.1; max-height: 7mm;
                            border-bottom: .1mm solid black;
                        }
                        .physical {
                            font-size: 3pt; text-align: center; color: black; font-weight: bold;
                            max-width: 36mm; max-height: 7mm; line-height: 1;
                        }
                        .barcode { display: flex; justify-content: center; }
                        .barcode svg { max-width: 33mm; height: 8mm; max-height: 12mm; }
                        .barcode-text { font-size: 5pt; font-family: monospace; text-align: center; line-height: 1; margin-top: 1mm; }
                    </style>
                </head>
                <body>
                    ${stickersHtml}
                    <script>
                        window.onload = function() {
                            window.print();
                            window.onafterprint = function() { window.close(); };
                        };
                    <\\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        });
    });
});
</script>
