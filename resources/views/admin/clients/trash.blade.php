<!DOCTYPE html>
@include('admin.main.html')
<head>
    <title>سلة المحذوفات - أرشيف القاهرة الجديدة</title>
    @include('admin.main.meta')
</head>
<body>
    <div class="wrapper">
        @include('admin.main.topbar')
        @include('admin.main.sidebar')
        <div class="content-page">
            <div class="container-fluid">
                {{-- Header --}}
                <div class="mt-3 mb-4 page-header">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="page-icon">
                                <div class="avatar avatar-lg bg-danger-subtle rounded-3">
                                    <i class="ti ti-trash fs-2 text-danger"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <h2 class="mb-1 page-title">سلة المحذوفات</h2>
                            <div class="text-secondary">
                                <span class="badge bg-danger-subtle text-danger me-2">{{ $totalTrashed }} عميل محذوف</span>
                                يمكن استعادتهم أو حذفهم نهائياً
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="gap-2 btn-list">
                                <a href="{{ route('admin.clients.index') }}" class="btn btn-primary">
                                    <i class="ti ti-arrow-right me-1"></i>العودة للعملاء
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Search --}}
                <div class="mb-3 border-0 card">
                    <div class="py-2 card-body">
                        <form action="{{ route('admin.clients.trash') }}" method="GET" class="row g-2 align-items-center">
                            <div class="col-md-10">
                                <div class="input-group">
                                    <span class="input-group-text bg-danger-subtle text-danger">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control"
                                           placeholder="بحث بالاسم أو الرقم القومي أو الموبايل..."
                                           value="{{ $searchTerm ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="ti ti-search me-1"></i>بحث
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Table --}}
                <div class="card">
                    {{-- Bulk Actions Bar --}}
                    <div id="bulkActionsBar" class="m-3 mb-0 alert alert-danger d-none">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="ti ti-checkbox me-2"></i>
                                <span id="selectedCount">0</span> عميل محدد
                            </div>
                            <div class="gap-2 d-flex">
                                <button type="button" class="btn btn-sm btn-success" onclick="bulkRestore()">
                                    <i class="ti ti-restore me-1"></i>استعادة المحدد
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="bulkForceDelete()">
                                    <i class="ti ti-trash-x me-1"></i>حذف نهائي
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="clearSelection()">
                                    <i class="ti ti-x me-1"></i>إلغاء
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 card-title">
                            <i class="ti ti-trash me-1"></i>العملاء المحذوفين
                        </h5>
                        <span class="badge bg-danger">{{ $totalTrashed }} عميل</span>
                    </div>
                    <div class="p-0 card-body">
                        @if($clients->count() > 0)
                        <div class="table-responsive">
                            <table class="table mb-0 table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40px;">
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                        </th>
                                        <th>العميل</th>
                                        <th>الرقم القومي</th>
                                        <th>الموبايل</th>
                                        <th>عدد الملفات</th>
                                        <th>تاريخ الحذف</th>
                                        <th class="text-center" style="width: 180px;">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($clients as $client)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input client-checkbox" value="{{ $client->id }}">
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $client->name }}</div>
                                            @if($client->client_code)
                                                <small class="text-muted">{{ $client->client_code }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $client->national_id ?? '-' }}</td>
                                        <td>{{ $client->mobile ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $client->files->count() }} ملف</span>
                                        </td>
                                        <td>
                                            <span class="text-danger">
                                                <i class="ti ti-clock me-1"></i>
                                                {{ $client->deleted_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <form action="{{ route('admin.clients.restore', $client->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" title="استعادة">
                                                        <i class="ti ti-restore"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.clients.force-delete', $client->id) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('هل أنت متأكد من الحذف النهائي؟\nلن يمكن استعادة هذا العميل بعد ذلك!');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="حذف نهائي">
                                                        <i class="ti ti-trash-x"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="py-5 text-center">
                            <div class="mx-auto mb-3 avatar-lg">
                                <span class="avatar-title bg-light text-success rounded-circle fs-1">
                                    <i class="ti ti-check"></i>
                                </span>
                            </div>
                            <h5 class="text-muted">سلة المحذوفات فارغة</h5>
                            <p class="mb-3 text-muted">لا يوجد عملاء محذوفين</p>
                            <a href="{{ route('admin.clients.index') }}" class="btn btn-primary">
                                <i class="ti ti-arrow-right me-1"></i>العودة للعملاء
                            </a>
                        </div>
                        @endif
                    </div>
                    @if($clients->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted fs-sm">
                                عرض {{ $clients->firstItem() }} إلى {{ $clients->lastItem() }} من {{ $clients->total() }} عميل
                            </div>
                            {{ $clients->links() }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('admin.main.scripts')

    <script>
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
                bulkBar.classList.remove('d-none');
                countSpan.textContent = selected.length;
            } else {
                bulkBar.classList.add('d-none');
            }

            const allCheckboxes = document.querySelectorAll('.client-checkbox');
            const selectAll = document.getElementById('selectAll');
            if (selectAll) {
                selectAll.checked = selected.length === allCheckboxes.length && allCheckboxes.length > 0;
                selectAll.indeterminate = selected.length > 0 && selected.length < allCheckboxes.length;
            }
        }

        function getSelectedClientIds() {
            return Array.from(document.querySelectorAll('.client-checkbox:checked')).map(cb => cb.value);
        }

        function clearSelection() {
            document.querySelectorAll('.client-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('selectAll').checked = false;
            updateBulkActionsBar();
        }

        // Bulk Force Delete
        function bulkForceDelete() {
            const clientIds = getSelectedClientIds();
            if (clientIds.length === 0) return;

            if (!confirm(`هل أنت متأكد من الحذف النهائي لـ ${clientIds.length} عميل؟\nلن يمكن استعادتهم بعد ذلك!`)) {
                return;
            }

            fetch('{{ route("admin.clients.bulk-force-delete") }}', {
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
        }

        // Bulk Restore
        function bulkRestore() {
            const clientIds = getSelectedClientIds();
            if (clientIds.length === 0) return;

            if (!confirm(`هل تريد استعادة ${clientIds.length} عميل؟`)) {
                return;
            }

            fetch('{{ route("admin.clients.bulk-restore") }}', {
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
                alert('حدث خطأ أثناء الاستعادة');
            });
        }
    </script>
</body>
</html>
