<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة باركود - {{ $client->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #fff;
            padding: 20px;
            direction: rtl;
        }
        .print-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        .print-header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .print-header p {
            color: #666;
            font-size: 14px;
        }
        .client-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .client-info h2 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .client-info p {
            margin: 5px 0;
            color: #555;
        }
        .barcodes-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .barcode-card {
            border: 2px solid #333;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            page-break-inside: avoid;
        }
        .barcode-card .file-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        .barcode-card .barcode-value {
            font-family: 'Courier New', monospace;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 2px;
            padding: 10px;
            background: #f0f0f0;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .barcode-card .barcode-image {
            margin: 15px 0;
        }
        .barcode-card .barcode-image svg {
            max-width: 100%;
            height: 60px;
        }
        .barcode-card .location {
            font-size: 11px;
            color: #666;
            margin-top: 10px;
        }
        .no-barcodes {
            text-align: center;
            padding: 50px;
            color: #666;
        }
        .print-actions {
            position: fixed;
            top: 20px;
            left: 20px;
            display: flex;
            gap: 10px;
        }
        .print-actions button {
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
        }
        .btn-print {
            background: #0d6efd;
            color: white;
        }
        .btn-close {
            background: #6c757d;
            color: white;
        }
        @media print {
            .print-actions {
                display: none;
            }
            body {
                padding: 0;
            }
            .barcode-card {
                border-width: 1px;
            }
        }
    </style>
</head>
<body>
    <div class="print-actions">
        <button class="btn-print" onclick="window.print()">
            🖨️ طباعة
        </button>
        <button class="btn-close" onclick="window.close()">
            ✕ إغلاق
        </button>
    </div>

    <div class="print-header">
        <h1>أرشيف القاهرة الجديدة</h1>
        <p>طباعة باركود ملفات العميل</p>
    </div>

    <div class="client-info">
        <h2>{{ $client->name }}</h2>
        <p><strong>كود العميل:</strong> {{ $client->client_code ?? '-' }}</p>
        <p><strong>الرقم القومي:</strong> {{ $client->national_id ?? '-' }}</p>
        <p><strong>عدد الملفات:</strong> {{ $client->files->count() }}</p>
    </div>

    @if($client->files->count() > 0)
    <div class="barcodes-grid">
        @foreach($client->files as $file)
        <div class="barcode-card">
            <div class="file-name">ملف: {{ $file->file_name }}</div>
            <div class="barcode-value">{{ $file->barcode }}</div>
            <div class="barcode-image">
                <svg id="barcode-{{ $file->id }}"></svg>
            </div>
            <div class="location">
                @if($file->room || $file->lane || $file->stand || $file->rack)
                {{ $file->room?->name ?? '' }} / {{ $file->lane?->name ?? '' }} / {{ $file->stand?->name ?? '' }} / {{ $file->rack?->name ?? '' }}
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($client->files as $file)
            @if($file->barcode)
            JsBarcode("#barcode-{{ $file->id }}", "{{ $file->barcode }}", {
                format: "CODE128",
                width: 2,
                height: 50,
                displayValue: false
            });
            @endif
            @endforeach
        });
    </script>
    @else
    <div class="no-barcodes">
        <h3>لا توجد ملفات بها باركود</h3>
        <p>لم يتم العثور على ملفات تحتوي على باركود لهذا العميل</p>
    </div>
    @endif
</body>
</html>
