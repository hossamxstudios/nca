<!DOCTYPE html>
@include('admin.main.html')

<head>
    <title>إدارة المستخدمين - أرشيف القاهرة الجديدة</title>
    @include('admin.main.meta')
</head>

<body>
    <div class="wrapper">
        @include('admin.main.topbar')
        @include('admin.main.sidebar')
        <div class="content-page">
            <div class="container-fluid">
                @include('admin.users.header')
                @include('admin.users.stats-cards')
                @include('admin.users.search-bar')
                @include('admin.users.table')
            </div>
        </div>
    </div>
    @include('admin.users.add-modal')
    @include('admin.users.edit-modal')
    @include('admin.users.delete-modal')
    @include('admin.users.change-password-modal')
    @include('admin.users.scripts')
    @include('admin.main.scripts')
</body>
</html>
