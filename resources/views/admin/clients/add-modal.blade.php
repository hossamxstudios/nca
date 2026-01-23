{{-- Add Client Modal --}}
<div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.clients.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addClientModalLabel">
                        <i class="ti ti-user-plus me-2"></i>إضافة عميل جديد
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">اسم العميل <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الرقم القومي</label>
                            <input type="text" name="national_id" class="form-control @error('national_id') is-invalid @enderror"
                                   value="{{ old('national_id') }}" maxlength="14">
                            @error('national_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">كود العميل</label>
                            <input type="text" name="client_code" class="form-control @error('client_code') is-invalid @enderror"
                                   value="{{ old('client_code') }}">
                            @error('client_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">صف الإكسيل</label>
                            <input type="number" name="excel_row_number" class="form-control @error('excel_row_number') is-invalid @enderror"
                                   value="{{ old('excel_row_number') }}">
                            @error('excel_row_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">التليفون</label>
                            <input type="text" name="telephone" class="form-control @error('telephone') is-invalid @enderror"
                                   value="{{ old('telephone') }}">
                            @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الموبايل</label>
                            <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror"
                                   value="{{ old('mobile') }}">
                            @error('mobile')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                                      rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>إلغاء
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i>حفظ العميل
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
