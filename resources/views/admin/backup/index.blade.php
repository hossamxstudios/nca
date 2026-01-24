<!DOCTYPE html>
@include('admin.main.html')

<head>
    <title>النسخ الاحتياطي - أرشيف القاهرة الجديدة</title>
    @include('admin.main.meta')
</head>

<body>
    <div class="wrapper">
        @include('admin.main.topbar')
        @include('admin.main.sidebar')
        <div class="content-page">
            <div class="container-fluid">
                {{-- Header --}}
                <div class="py-3 d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ti ti-database-export me-2 text-danger"></i>النسخ الاحتياطي</h4>
                    <button type="button" class="btn btn-danger" onclick="startBackup()" id="btnBackup">
                        <i class="ti ti-download me-1"></i>نسخة جديدة
                    </button>
                </div>

                {{-- History Table --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 card-title">سجل النسخ الاحتياطية</h5>
                    </div>
                    <div class="p-0 card-body">
                        <div class="table-responsive">
                            <table class="table mb-0 table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>اسم الملف</th>
                                        <th>الحجم</th>
                                        <th>الحالة</th>
                                        <th>بواسطة</th>
                                        <th>التاريخ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($backups as $backup)
                                    <tr>
                                        <td>
                                            <i class="ti ti-file-zip text-warning me-2"></i>
                                            {{ $backup->filename }}
                                        </td>
                                        <td>{{ $backup->size_formatted }}</td>
                                        <td>
                                            @if($backup->status === 'completed')
                                                <span class="badge bg-success-subtle text-success">مكتمل</span>
                                            @elseif($backup->status === 'pending')
                                                <span class="badge bg-warning-subtle text-warning">جاري</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">فشل</span>
                                            @endif
                                        </td>
                                        <td>{{ $backup->user->name ?? '-' }}</td>
                                        <td>{{ $backup->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="py-4 text-center text-muted">
                                            <i class="mb-2 ti ti-database-off fs-1 d-block"></i>
                                            لا توجد نسخ احتياطية بعد
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($backups->hasPages())
                    <div class="card-footer">
                        {{ $backups->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('admin.main.scripts')

    {{-- Backup Progress Modal --}}
    <div class="modal fade" id="backupModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="p-4 text-center modal-content">
                <div class="mx-auto mb-3 spinner-border text-danger" style="width:3rem;height:3rem;"></div>
                <p class="mb-1">جاري إنشاء النسخة...</p>
                <small class="text-muted">لا تغلق الصفحة</small>
            </div>
        </div>
    </div>

    <script>
        async function startBackup() {
            const btn = document.getElementById('btnBackup');
            const modal = new bootstrap.Modal(document.getElementById('backupModal'));

            btn.disabled = true;
            modal.show();

            try {
                if ('showSaveFilePicker' in window) {
                    const handle = await window.showSaveFilePicker({
                        suggestedName: 'backup_' + new Date().toISOString().slice(0,10) + '.zip',
                        types: [{ accept: { 'application/zip': ['.zip'] } }]
                    });
                    const response = await fetch('{{ route("admin.backup.download") }}');
                    if (!response.ok) throw new Error('Backup failed');
                    const blob = await response.blob();
                    const writable = await handle.createWritable();
                    await writable.write(blob);
                    await writable.close();
                    modal.hide();
                    location.reload();
                } else {
                    window.location.href = '{{ route("admin.backup.download") }}';
                }
            } catch (err) {
                modal.hide();
                if (err.name !== 'AbortError') {
                    window.location.href = '{{ route("admin.backup.download") }}';
                }
            } finally {
                btn.disabled = false;
            }
        }
    </script>
</body>
</html>
