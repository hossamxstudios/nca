{{-- Delete Client Modals - One per client --}}
@foreach($clients as $client)
<div class="modal fade" id="deleteClientModal_{{ $client->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.clients.destroy', $client->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="ti ti-trash me-2"></i>تأكيد الحذف
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="mb-3">
                        <i class="ti ti-alert-triangle text-danger" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="mb-2">هل أنت متأكد من حذف هذا العميل؟</h5>
                    <p class="text-muted mb-0">
                        <strong class="text-dark">{{ $client->name }}</strong>
                    </p>
                    <p class="text-danger small mt-2">
                        <i class="ti ti-alert-circle me-1"></i>
                        سيتم حذف جميع الملفات المرتبطة بهذا العميل. هذا الإجراء لا يمكن التراجع عنه.
                    </p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>إلغاء
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="ti ti-trash me-1"></i>نعم، احذف
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
