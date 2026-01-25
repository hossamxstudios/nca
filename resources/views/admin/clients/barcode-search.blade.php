<div class="mb-2 border-0 shadow card">
    <div class="py-2 card-body">
        <form action="{{ route('admin.clients.index') }}" method="GET" class="row g-2 align-items-center" id="barcodeForm">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-primary-subtle text-primary">
                        <i class="ti ti-barcode"></i>
                    </span>
                    <input type="text" name="barcode" id="barcodeInput" class="form-control" placeholder="امسح الباركود هنا أو أدخله يدوياً..." value="" autofocus>
                    @if($barcode ?? false)
                    <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-x"></i>
                    </a>
                    @endif
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="ti ti-scan me-1"></i>بحث بالباركود
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const barcodeInput = document.getElementById('barcodeInput');

        // Arabic to English keyboard mapping (standard Arabic layout - same on Mac & Windows)
        const arabicToEnglish = {
            // Arabic-Indic numerals
            '١': '1', '٢': '2', '٣': '3', '٤': '4', '٥': '5', '٦': '6', '٧': '7', '٨': '8', '٩': '9', '٠': '0',
            // Top letter row (QWERTY positions)
            'ض': 'q', 'ص': 'w', 'ث': 'e', 'ق': 'r', 'ف': 't', 'غ': 'y', 'ع': 'u', 'ه': 'i', 'خ': 'o', 'ح': 'p',
            // Middle letter row (ASDF positions)
            'ش': 'a', 'س': 's', 'ي': 'd', 'ب': 'f', 'ل': 'g', 'ا': 'h', 'ت': 'j', 'ن': 'k', 'م': 'l',
            // Bottom letter row (ZXCV positions)
            'ئ': 'z', 'ء': 'x', 'ؤ': 'c', 'ر': 'v', 'ى': 'n', 'ة': 'm'
        };

        // Convert Arabic to English
        function convertToEnglish(text) {
            return text.split('').map(char => arabicToEnglish[char] || char).join('');
        }

        // Global keyboard capture - redirect typing to barcode input
        document.addEventListener('keydown', function(e) {
            const activeElement = document.activeElement;
            const isInputField = activeElement.tagName === 'INPUT' ||
                                activeElement.tagName === 'TEXTAREA' ||
                                activeElement.isContentEditable;

            // If not in an input field, focus barcode input and convert key
            if (!isInputField && e.key.length === 1) {
                e.preventDefault();
                barcodeInput.focus();
                const convertedKey = convertToEnglish(e.key);
                // Only add if it's alphanumeric after conversion
                if (/^[a-zA-Z0-9\-_]$/.test(convertedKey)) {
                    barcodeInput.value = convertedKey;
                }
            }

            // Global Enter key when not in input - submit if barcode has value
            if (!isInputField && e.key === 'Enter' && barcodeInput.value.trim()) {
                e.preventDefault();
                document.getElementById('barcodeForm').submit();
            }
        });

        // On Enter key in barcode input, submit form
        barcodeInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (barcodeInput.value.trim()) {
                    document.getElementById('barcodeForm').submit();
                }
            }
        });

        // When input receives focus, select all (so new scan replaces old)
        barcodeInput.addEventListener('focus', function() {
            this.select();
        });

        // Convert Arabic to English on input (when typing directly in the field)
        barcodeInput.addEventListener('input', function(e) {
            const converted = convertToEnglish(this.value);
            // Keep only valid characters after conversion
            this.value = converted.replace(/[^A-Za-z0-9\-_]/g, '');
        });

        // Clear and replace on paste
        barcodeInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            // Convert Arabic to English then filter
            const converted = convertToEnglish(pastedText);
            this.value = converted.replace(/[^A-Za-z0-9\-_]/g, '');
        });
    });
</script>
