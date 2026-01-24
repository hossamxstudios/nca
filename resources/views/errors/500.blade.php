<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - خطأ في الخادم</title>
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
            color: #141414;
            line-height: 1;
            text-shadow: 4px 4px 0 rgba(183, 183, 183, 0.1);
        }
        .error-icon {
            width: 120px;
            height: 120px;
            background: #141414;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            box-shadow: 0 10px 40px rgba(124, 124, 124, 0.3);
        }
        .error-icon i {
            font-size: 60px;
            color: white;
        }
        .error-title {
            font-size: 28px;
            font-weight: 600;
            color: #141414;
            margin-bottom: 15px;
        }
        .error-message {
            font-size: 16px;
            color: #141414;
            margin-bottom: 30px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }
        .btn-home {
            background: #161616;
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
            background: #141414;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(156, 156, 156, 0.4);
        }
        .btn-refresh {
            background: transparent;
            color: #141414;
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
            color: #141414;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="ti ti-server-off"></i>
        </div>
        <div class="error-code">500</div>
        <h1 class="error-title">خطأ في الخادم</h1>
        <p class="error-message">عذراً، حدث خطأ غير متوقع. فريقنا يعمل على إصلاح المشكلة.</p>
        <div>
            <a href="javascript:location.reload()" class="btn-refresh">
                <i class="ti ti-refresh"></i>
                إعادة المحاولة
            </a>
            <a href="{{ url('/') }}" class="btn-home">
                <i class="ti ti-home"></i>
                العودة للرئيسية
            </a>
        </div>
    </div>
</body>
</html>
