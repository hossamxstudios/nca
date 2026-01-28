<div class="card">
    <div id="bulkActionsBar" class="m-3 mb-0 alert alert-primary d-none">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="ti ti-checkbox me-2"></i>
                <span id="selectedCount">0</span> عميل محدد
            </div>
            <div class="gap-2 d-flex">
                <button type="button" class="btn btn-sm btn-primary" onclick="bulkPrintBarcodes()">
                    <i class="ti ti-printer me-1"></i>طباعة الباركود
                </button>
                @can('clients.delete')
                <button type="button" class="btn btn-sm btn-light text-danger" onclick="bulkDelete()">
                    <i class="ti ti-trash me-1"></i>حذف المحدد
                </button>
                @endcan
                <button type="button" class="btn btn-sm btn-light text-primary" onclick="clearSelection()">
                    <i class="ti ti-x me-1"></i>إلغاء التحديد
                </button>
            </div>
        </div>
    </div>
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 card-title">
            <i class="ti ti-table me-1"></i>قائمة العملاء
        </h5>
        <span class="badge bg-primary">{{ $clients->total() }} عميل</span>
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
                        <th>رقم الملف</th>
                        <th>العنوان الجغرافي</th>
                        <th>الموقع الفعلي</th>
                        <th class="text-center">الصفحات</th>
                        <th>الباركود</th>
                        <th class="text-center" style="width: 150px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clients as $client)
                    @php $filesCount = $client->files->count(); $firstFile = $client->files->first();
                    @endphp
                    <tr class="client-row">
                        <td rowspan="{{ $filesCount > 0 ? $filesCount : 1 }}">
                            <input type="checkbox" class="form-check-input client-checkbox" value="{{ $client->id }}">
                        </td>
                        <td rowspan="{{ $filesCount > 0 ? $filesCount : 1 }}">
                            <div class="fw-semibold">{{ $client->name }}</div>
                            @if($client->excel_row_number)
                                <span class="text-dark fs-5 fw-bold">صف: {{ $client->excel_row_number }}</span>
                            @endif
                        </td>
                        @if($firstFile)
                        <td>
                            <span class="text-white badge bg-dark fs-5">{{ $firstFile->file_name }}</span>
                        </td>
                        <td>
                            @if($firstFile->land)
                            <div class="flex-wrap gap-1 d-flex">
                                <span class="border badge bg-light text-dark fs-5"><span class="text-dark">الحي:</span> {{ $firstFile->land?->district?->name ?? '-' }}</span>
                                <span class="border badge bg-light text-dark fs-5"><span class="text-dark">المجاورة:</span> {{ $firstFile->land?->zone?->name ?? '-' }}</span>
                                <span class="border badge bg-light text-dark fs-5"><span class="text-dark">المنطقة:</span> {{ $firstFile->land?->area?->name ?? '-' }}</span>
                                <span class="border badge bg-light text-dark fs-5"><span class="text-dark">القطعة:</span> {{ $firstFile->land?->land_no ?? '-' }}</span>
                            </div>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($firstFile->room || $firstFile->lane || $firstFile->stand || $firstFile->rack)
                            <div class="flex-wrap gap-1 d-flex">
                                <span class="border badge bg-light text-dark fs-5"><span class="text-dark">غرفة:</span> {{ $firstFile->room?->name }}</span>
                                <span class="border badge bg-light text-dark fs-5"><span class="text-dark">ممر:</span> {{ $firstFile->lane?->name }}</span>
                                <span class="border badge bg-light text-dark fs-5"><span class="text-dark">استاند:</span> {{ $firstFile->stand?->name }}</span>
                                <span class="border badge bg-light text-dark fs-5"><span class="text-dark">رف:</span> {{ $firstFile->rack?->name }}</span>
                            </div>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary-subtle text-primary fs-6">{{ $firstFile->pages_count ?? 0 }}</span>
                        </td>
                        <td>
                            @if($firstFile->barcode)
                                <code class="text-primary">{{ $firstFile->barcode }}</code>
                            @else
                                <span class="text-dark">-</span>
                            @endif
                        </td>
                        @else
                        <td colspan="5" class="text-center text-dark">
                            <i class="ti ti-folder-off me-1"></i>لا توجد ملفات
                        </td>
                        @endif
                        <td rowspan="{{ $filesCount > 0 ? $filesCount : 1 }}" class="text-center">
                            <div class="gap-1 d-flex justify-content-center">
                                @can('clients.create')
                                <button type="button" class="px-2 text-white btn btn-icon btn-sm bg-primary" title="إضافة ملف" data-bs-toggle="modal" data-bs-target="#addFileModal_{{ $client->id }}">
                                    <i class="ti ti-file-plus fs-5"></i>
                                </button>
                                @endcan
                                <button type="button" class="px-2 btn btn-icon btn-sm bg-primary-subtle text-primary" title="طباعة" data-bs-toggle="modal" data-bs-target="#printBarcodeModal_{{ $client->id }}">
                                    <i class="ti ti-printer fs-5"></i>
                                </button>
                                <a href="{{ route('admin.clients.show', $client->id) }}" class="px-2 btn btn-icon btn-sm bg-info-subtle text-info" title="عرض">
                                    <i class="ti ti-eye fs-5"></i>
                                </a>
                                @can('clients.edit')
                                <button type="button" class="px-2 btn btn-icon btn-sm bg-warning-subtle text-warning" title="تعديل" data-bs-toggle="modal" data-bs-target="#editClientModal_{{ $client->id }}">
                                    <i class="ti ti-edit fs-5"></i>
                                </button>
                                @endcan
                                @can('clients.delete')
                                <button type="button" class="px-2 btn btn-icon btn-sm bg-danger-subtle text-danger" title="حذف" data-bs-toggle="modal" data-bs-target="#deleteClientModal_{{ $client->id }}">
                                    <i class="ti ti-trash fs-5"></i>
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @foreach($client->files->skip(1) as $file)
                    <tr class="file-subrow">
                        <td>
                            <span class="text-white badge bg-dark fs-5">{{ $file->file_name }}</span>
                        </td>
                        <td>
                            @if($file->land)
                            <div class="flex-wrap gap-1 d-flex">
                                <span class="border badge bg-light text-dark fs-5"><span class="text-dark">الحي:</span> {{ $file->land?->district?->name ?? '-' }}</span>
                                <span class="border badge bg-light text-dark fs-5"><span class="text-dark">المجاورة:</span> {{ $file->land?->zone?->name ?? '-' }}</span>
                                <span class="border badge bg-light text-dark fs-5"><span class="text-dark">المنطقة:</span> {{ $file->land?->area?->name ?? '-' }}</span>
                                <span class="border badge bg-light text-dark fs-5"><span class="text-dark">القطعة:</span> {{ $file->land?->land_no ?? '-' }}</span>
                            </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($file->room || $file->lane || $file->stand || $file->rack)
                            <div class="flex-wrap gap-1 d-flex">
                                <span class="border badge bg-light text-dark fs-5"><span class="text-dark">غرفة:</span> {{ $file->room?->name }}</span>
                                <span class="border badge bg-light text-dark fs-5"><span class="text-dark">ممر:</span> {{ $file->lane?->name }}</span>
                                <span class="border badge bg-light text-dark fs-5"><span class="text-dark">استاند:</span> {{ $file->stand?->name }}</span>
                                <span class="border badge bg-light text-dark fs-5"><span class="text-dark">رف:</span> {{ $file->rack?->name }}</span>
                            </div>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info-subtle text-info">{{ $file->pages_count ?? 0 }}</span>
                        </td>
                        <td>
                            @if($file->barcode)
                                <code class="text-primary">{{ $file->barcode }}</code>
                            @else
                                <span class="text-dark">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="py-5 text-center">
            <div class="mx-auto mb-3 avatar-lg">
                <span class="avatar-title bg-light text-muted rounded-circle fs-1">
                    @if(isset($requiresSearch) && $requiresSearch && !request('search') && !request('barcode'))
                    <i class="ti ti-search"></i>
                    @else
                    <i class="ti ti-users-minus"></i>
                    @endif
                </span>
            </div>
            @if(isset($requiresSearch) && $requiresSearch && !request('search') && !request('barcode'))
                <h5 class="text-muted">ابحث عن عميل</h5>
                <p class="mb-3 text-muted">يرجى استخدام البحث أو مسح الباركود للعثور على العملاء</p>
            @else
                <h5 class="text-muted">لا يوجد عملاء</h5>
                <p class="mb-3 text-muted">لم يتم العثور على أي عملاء مطابقين لمعايير البحث</p>
                @can('clients.create')
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClientModal">
                        <i class="ti ti-plus me-1"></i>إضافة عميل جديد
                    </button>
                @endcan
            @endif
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

<style>
.file-subrow {
    background-color: rgba(0,0,0,0.02) !important;
}
.file-subrow td {
    border-top: 1px dashed #dee2e6 !important;
}
</style>
