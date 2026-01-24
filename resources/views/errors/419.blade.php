<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - انتهت صلاحية الصفحة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-container {
            text-align: center;
            padding: 40px;
        }
        .error-code {
            font-size: 150px;
            font-weight: 800;
            color: #494949;
            line-height: 1;
            text-shadow: 4px 4px 0 rgba(200, 200, 200, 0.1);
        }
        .error-icon {
            width: 120px;
            height: 120px;
            background: #121212;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            box-shadow: 0 10px 40px rgba(200, 200, 200, 0.3);
        }
        .error-icon i {
            font-size: 60px;
            color: white;
        }
        .error-title {
            font-size: 28px;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 15px;
        }
        .error-message {
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 30px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }
        .btn-home {
            background: #1d1d1d;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-home:hover {
            background: #333;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }
        .btn-refresh {
            background: transparent;
            color: #6c757d;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #dee2e6;
            margin-right: 10px;
        }
        .btn-refresh:hover {
            background: #f8f9fa;
            color: #343a40;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="ti ti-clock-off"></i>
        </div>
        <div class="error-code">419</div>
        <h1 class="error-title">انتهت صلاحية الصفحة</h1>
        <p class="error-message">انتهت صلاحية الجلسة الخاصة بك. يرجى تحديث الصفحة والمحاولة مرة أخرى.</p>
        <div>
            <a href="javascript:location.reload()" class="btn-refresh">
                <i class="ti ti-refresh"></i>
                تحديث الصفحة
            </a>
            <a href="{{ url('/') }}" class="btn-home">
                <i class="ti ti-home"></i>
                العودة للرئيسية
            </a>
        </div>
    </div>
</body>
</html>
