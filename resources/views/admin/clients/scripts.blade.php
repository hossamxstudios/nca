<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fix aria-hidden focus issue - blur focused element when modal hides
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('hide.bs.modal', function() {
            if (document.activeElement && this.contains(document.activeElement)) {
                document.activeElement.blur();
            }
        });
    });

    // Select All Checkbox
    document.getElementById('selectAll')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.client-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBulkActionsBar();
    });

    // Individual checkbox change
    document.querySelectorAll('.client-checkbox').forEach(cb => {
        cb.addEventListener('change', updateBulkActionsBar);
    });

    // Update bulk actions bar visibility
    function updateBulkActionsBar() {
        const selected = document.querySelectorAll('.client-checkbox:checked');
        const bulkBar = document.getElementById('bulkActionsBar');
        const countSpan = document.getElementById('selectedCount');

        if (selected.length > 0) {
            bulkBar?.classList.remove('d-none');
            if (countSpan) countSpan.textContent = selected.length;
        } else {
            bulkBar?.classList.add('d-none');
        }

        const allCheckboxes = document.querySelectorAll('.client-checkbox');
        const selectAll = document.getElementById('selectAll');
        if (selectAll) {
            selectAll.checked = selected.length === allCheckboxes.length && allCheckboxes.length > 0;
            selectAll.indeterminate = selected.length > 0 && selected.length < allCheckboxes.length;
        }
    }

    // Get selected client IDs
    window.getSelectedClientIds = function() {
        return Array.from(document.querySelectorAll('.client-checkbox:checked')).map(cb => cb.value);
    };

    // Clear selection
    window.clearSelection = function() {
        document.querySelectorAll('.client-checkbox').forEach(cb => cb.checked = false);
        const selectAll = document.getElementById('selectAll');
        if (selectAll) selectAll.checked = false;
        updateBulkActionsBar();
    };

    // Bulk Delete
    window.bulkDelete = function() {
        const clientIds = getSelectedClientIds();
        if (clientIds.length === 0) return;

        if (!confirm(`هل أنت متأكد من حذف ${clientIds.length} عميل؟\nسيتم حذف جميع الملفات المرتبطة بهم.`)) {
            return;
        }

        fetch('{{ route("admin.clients.bulk-delete") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ client_ids: clientIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.message || 'حدث خطأ');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء الحذف');
        });
    };

    // Bulk Print Barcodes
    window.bulkPrintBarcodes = function() {
        const clientIds = getSelectedClientIds();
        if (clientIds.length === 0) return;

        fetch('{{ route("admin.clients.bulk-print-data") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ client_ids: clientIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                // Show print option modal
                showBulkPrintOptionsModal(data.data, data.total_stickers);
            } else {
                alert('لا توجد ملفات بها باركود للعملاء المحددين');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء جلب البيانات');
        });
    };

    // Show bulk print options modal
    function showBulkPrintOptionsModal(filesData, totalStickers) {
        const filesCount = filesData.length;
        const choice = confirm(
            `اختر نوع الطباعة:\n\n` +
            `اضغط "موافق" لطباعة كل الصفحات (${totalStickers} استيكر)\n` +
            `اضغط "إلغاء" لطباعة أول صفحة فقط لكل ملف (${filesCount} استيكر)`
        );

        printBulkBarcodes(filesData, totalStickers, choice ? 'all' : 'first');
    }

    // Print bulk barcodes
    function printBulkBarcodes(filesData, totalStickers, printOption = 'all') {
        const tempContainer = document.createElement('div');
        tempContainer.style.position = 'absolute';
        tempContainer.style.left = '-9999px';
        document.body.appendChild(tempContainer);

        filesData.forEach((file, index) => {
            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.id = `bulkBarcode_${index}`;
            tempContainer.appendChild(svg);

            JsBarcode(`#bulkBarcode_${index}`, file.barcode, {
                format: "CODE128",
                width: 1.2,
                height: 35,
                displayValue: false,
                margin: 0
            });
        });

        setTimeout(() => {
            let stickersHtml = '';
            let actualTotalStickers = 0;
            filesData.forEach((file, index) => {
                const svgElement = document.getElementById(`bulkBarcode_${index}`);
                const barcodeSvg = svgElement ? svgElement.outerHTML : '';
                const copies = printOption === 'first' ? 1 : (file.pages_count || 1);
                actualTotalStickers += copies;

                for (let i = 0; i < copies; i++) {
                    stickersHtml += `
                        <div class="sticker">
                            <div class="client-name">${file.client_name}</div>
                            <div class="geo">${file.geo || '-'}</div>
                            <div class="physical">${file.physical || '-'}</div>
                            <div class="barcode">${barcodeSvg}</div>
                            <div class="barcode-text">${file.barcode}</div>
                        </div>
                    `;
                }
            });
            totalStickers = actualTotalStickers;

            document.body.removeChild(tempContainer);

            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html dir="rtl">
                <head>
                    <title>طباعة الباركود - ${filesData.length} ملف - ${totalStickers} استيكر</title>
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
                    <\\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }, 100);
    }

    // Auto-submit barcode search on scan
    document.getElementById('barcodeInput')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            this.form.submit();
        }
    });
});
</script>
