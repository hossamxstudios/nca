{{-- Add Client Modal --}}
<div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="border-0 shadow modal-content">
            <form action="{{ route('admin.clients.store') }}" method="POST">
                @csrf
                <div class="py-3 modal-header bg-primary-subtle text-dark">
                    <h5 class="modal-title" id="addClientModalLabel">
                        <i class="ti ti-user-plus me-2"></i>عميل جديد
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="p-4 modal-body">
                    {{-- البيانات الأساسية --}}
                    <div class="mb-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" name="name" id="clientName"
                                           class="form-control form-control-lg @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}" placeholder="اسم العميل" required autofocus>
                                    <label for="clientName">اسم العميل <span class="text-danger">*</span></label>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating">
                                    <input type="text" name="file_name" id="fileName"
                                           class="form-control @error('file_name') is-invalid @enderror"
                                           value="{{ old('file_name') }}" placeholder="رقم الملف">
                                    <label for="fileName">رقم الملف</label>
                                    @error('file_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating">
                                    <input type="text" name="land_no" id="landNo"
                                           class="form-control @error('land_no') is-invalid @enderror"
                                           value="{{ old('land_no') }}" placeholder="رقم القطعة">
                                    <label for="landNo">رقم القطعة</label>
                                    @error('land_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- بيانات إضافية - قابلة للطي --}}
                    <div class="accordion accordion-flush" id="additionalData">
                        <div class="rounded border accordion-item">
                            <h2 class="accordion-header">
                                <button class="px-3 py-2 accordion-button collapsed text-muted small" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#moreDetails">
                                    <i class="ti ti-plus me-2"></i>بيانات إضافية (اختياري)
                                </button>
                            </h2>
                            <div id="moreDetails" class="accordion-collapse collapse" data-bs-parent="#additionalData">
                                <div class="pt-2 accordion-body">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="form-floating">
                                                <input type="text" name="national_id" id="nationalId"
                                                       class="form-control @error('national_id') is-invalid @enderror"
                                                       value="{{ old('national_id') }}" maxlength="14" placeholder="الرقم القومي">
                                                <label for="nationalId">الرقم القومي</label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-floating">
                                                <input type="text" name="mobile" id="mobile"
                                                       class="form-control @error('mobile') is-invalid @enderror"
                                                       value="{{ old('mobile') }}" placeholder="الموبايل">
                                                <label for="mobile">الموبايل</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <textarea name="notes" id="notes"
                                                          class="form-control @error('notes') is-invalid @enderror"
                                                          placeholder="ملاحظات" style="height: 80px">{{ old('notes') }}</textarea>
                                                <label for="notes">ملاحظات</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 pt-0 pb-4 border-0 modal-footer">
                    <button type="button" class="px-4 btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="px-4 btn btn-primary">
                        <i class="ti ti-check me-1"></i>حفظ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
