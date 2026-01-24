<!DOCTYPE html>
@include('admin.main.html')
<head>
    <title>إدارة العملاء - أرشيف القاهرة الجديدة</title>
    @include('admin.main.meta')
</head>
<body>
    <div class="wrapper">
        @include('admin.main.topbar')
        @include('admin.main.sidebar')
        <div class="content-page">
            <div class="container-fluid">
                @include('admin.clients.header')
                @include('admin.clients.stats-cards')
                @include('admin.clients.search-bar')
                @include('admin.clients.barcode-search')
                @include('admin.clients.table')
            </div>
        </div>
    </div>
    @can('clients.create')
        @include('admin.clients.add-modal')
        @include('admin.clients.add-file-modal')
    @endcan
    @can('clients.edit')
        @include('admin.clients.edit-modal')
    @endcan
    @can('clients.delete')
        @include('admin.clients.delete-modal')
    @endcan
    @include('admin.clients.print-barcode-modal')
    @include('admin.clients.print-type-modal')
    @include('admin.clients.scripts')
    @include('admin.main.scripts')
</body>
</html>
