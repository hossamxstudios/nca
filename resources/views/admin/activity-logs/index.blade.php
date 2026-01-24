<!DOCTYPE html>
@include('admin.main.html')
<head>
    <title>سجل النشاطات - أرشيف القاهرة الجديدة</title>
    @include('admin.main.meta')
</head>
<body>
    <div class="wrapper">
        @include('admin.main.topbar')
        @include('admin.main.sidebar')
        <div class="content-page">
            <div class="container-fluid">
                @include('admin.activity-logs.header')
                @include('admin.activity-logs.stats-cards')
                @include('admin.activity-logs.filters')
                @include('admin.activity-logs.table')
            </div>
        </div>
    </div>
    @include('admin.activity-logs.modals')
    @include('admin.activity-logs.scripts')
    @include('admin.main.scripts')
</body>
</html>
