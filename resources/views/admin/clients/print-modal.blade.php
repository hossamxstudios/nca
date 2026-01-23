{{-- Print Barcode Modal --}}
<div class="modal fade" id="printBarcodeModal" tabindex="-1" aria-labelledby="printBarcodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printBarcodeModalLabel">
                    <i class="ti ti-printer me-2"></i>طباعة الباركود
                    <span class="badge bg-primary ms-2" id="filesCount"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Client Name --}}
                <div class="mb-3 text-center">
                    <h6 class="mb-1 text-muted">العميل</h6>
                    <strong id="printClientName"></strong>
                </div>

                {{-- Preview Container for multiple stickers --}}
                <div class="mb-3">
                    <label class="text-center form-label text-muted d-block">معاينة الاستيكرات (38×25 مم)</label>
                </div>

                {{-- Stickers Grid --}}
                <div id="stickersPreview" class="flex-wrap gap-3 d-flex justify-content-center">
                    {{-- Stickers will be generated here by JS --}}
                </div>

                {{-- Info --}}
                <div class="mt-4 text-center">
                    <small class="text-muted">
                        <i class="ti ti-info-circle me-1"></i>
                        سيتم طباعة استيكر لكل صفحة في الملف
                    </small>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>إغلاق
                </button>
                <button type="button" class="btn btn-primary" onclick="printAllBarcodes()">
                    <i class="ti ti-printer me-1"></i>طباعة الكل
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Print Styles --}}
<style>
    /* Sticker dimensions: 38x25mm - scaled 4x for preview (152x100px) */
    .barcode-sticker {
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

    .sticker-client-name {
        font-size: 8px;
        font-weight: bold;
        text-align: center;
        line-height: 1.1;
        width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sticker-geo {
        font-size: 6px;
        text-align: center;
        color: #666;
        line-height: 1.1;
        width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .barcode-sticker svg {
        max-width: 140px;
        height: 45px;
    }

    .sticker-barcode-text {
        font-size: 7px;
        font-family: monospace;
        text-align: center;
        line-height: 1.1;
    }

    .sticker-file-name {
        font-size: 5px;
        color: #999;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 100%;
    }

    @media print {
        @page {
            size: 38mm 25mm;
            margin: 0;
        }

        body * {
            visibility: hidden;
        }

        #printArea, #printArea * {
            visibility: visible;
        }

        #printArea {
            position: absolute;
            left: 0;
            top: 0;
        }

        .print-sticker {
            width: 38mm;
            height: 25mm;
            padding: 1mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            page-break-after: always;
        }

        .print-sticker .sticker-client-name {
            font-size: 6pt;
            font-weight: bold;
        }

        .print-sticker .sticker-geo {
            font-size: 4pt;
            color: #333;
        }

        .print-sticker svg {
            max-width: 34mm;
            height: 12mm;
        }

        .print-sticker .sticker-barcode-text {
            font-size: 5pt;
            font-family: monospace;
        }
    }
</style>
