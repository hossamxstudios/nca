{{-- Delete User Modals - One modal per user --}}
@foreach($users as $user)
@if($user->id != auth()->id() && !$user->trashed())
<div class="modal fade" id="deleteUserModal_{{ $user->id }}" tabindex="-1" aria-labelledby="deleteUserModalLabel_{{ $user->id }}">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="text-white modal-header bg-danger">
                <h5 class="modal-title" id="deleteUserModalLabel_{{ $user->id }}">
                    <i class="ti ti-alert-triangle me-2"></i>تأكيد الحذف
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="py-4 text-center modal-body">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="mb-3 rounded-circle" width="80" height="80">
                @else
                    <div class="mx-auto mb-3 avatar-lg">
                        <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-2">
                            {{ $user->initials }}
                        </span>
                    </div>
                @endif
                <h5 class="mb-2">{{ $user->name }}</h5>
                <p class="mb-0 text-muted">{{ $user->email }}</p>
                <hr>
                <p class="mb-1">هل أنت متأكد من حذف هذا المستخدم؟</p>
                <small class="text-muted">يمكن استعادته لاحقاً من سلة المحذوفات</small>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>إلغاء
                </button>
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="ti ti-trash me-1"></i>نعم، احذف
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach
