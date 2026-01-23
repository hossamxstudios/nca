{{-- Edit Client Modals - One per client --}}
@foreach($clients as $client)
<div class="modal fade" id="editClientModal_{{ $client->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.clients.update', $client->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti ti-user-edit me-2"></i>تعديل بيانات العميل
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">اسم العميل <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $client->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الرقم القومي</label>
                            <input type="text" name="national_id" class="form-control" value="{{ $client->national_id }}" maxlength="14">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">كود العميل</label>
                            <input type="text" name="client_code" class="form-control" value="{{ $client->client_code }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">صف الإكسيل</label>
                            <input type="number" name="excel_row_number" class="form-control" value="{{ $client->excel_row_number }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">التليفون</label>
                            <input type="text" name="telephone" class="form-control" value="{{ $client->telephone }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الموبايل</label>
                            <input type="text" name="mobile" class="form-control" value="{{ $client->mobile }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="3">{{ $client->notes }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>إلغاء
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i>حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
