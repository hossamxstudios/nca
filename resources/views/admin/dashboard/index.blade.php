<!DOCTYPE html>
@include('admin.main.html')

<head>
    <title>لوحة التحكم - أرشيف القاهرة الجديدة</title>
    @include('admin.main.meta')
</head>

<body>
    <div class="wrapper">
        @include('admin.main.topbar')
        @include('admin.main.sidebar')
        <div class="content-page">
            <div class="container-fluid">
                {{-- Welcome Section --}}
                @include('admin.dashboard.welcome')
                {{-- Stats Cards Section --}}
                @include('admin.dashboard.stats-cards')
                {{-- Quick Actions & Latest Clients Row --}}
                <div class="row">
                    {{-- Quick Actions (1/3) --}}
                    <div class="col-xl-12 col-lg-12">
                        @include('admin.dashboard.quick-actions')
                    </div>
                    {{-- Latest Clients Table (2/3) --}}
                    {{-- <div class="col-xl-8 col-lg-7">
                        @include('admin.dashboard.latest-clients')
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
    @include('admin.main.scripts')
</body>
</html>
