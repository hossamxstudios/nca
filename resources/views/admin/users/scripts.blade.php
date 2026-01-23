<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all checkbox functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const bulkActionsBar = document.getElementById('bulkActionsBar');
    const selectedCountSpan = document.getElementById('selectedCount');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            document.querySelectorAll('.user-checkbox').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });
    }

    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    function updateBulkActions() {
        const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
        if (bulkActionsBar) {
            bulkActionsBar.classList.toggle('d-none', checkedCount === 0);
        }
        if (selectedCountSpan) {
            selectedCountSpan.textContent = checkedCount;
        }
        // Update select all checkbox state
        const totalCheckboxes = document.querySelectorAll('.user-checkbox').length;
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = checkedCount === totalCheckboxes && totalCheckboxes > 0;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCheckboxes;
        }
    }

    // Blur focused element on modal hide to prevent aria-hidden warning
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('hide.bs.modal', function() {
            if (document.activeElement && this.contains(document.activeElement)) {
                document.activeElement.blur();
            }
        });
    });
});

// Bulk delete functionality
function bulkDelete() {
    const selectedIds = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
    if (selectedIds.length === 0) return;

    if (confirm(`هل أنت متأكد من حذف ${selectedIds.length} مستخدم؟`)) {
        fetch('{{ route("admin.users.bulk-delete") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ ids: selectedIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'حدث خطأ');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء الحذف');
        });
    }
}

// Clear selection
function clearSelection() {
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
    }
    const bulkActionsBar = document.getElementById('bulkActionsBar');
    if (bulkActionsBar) {
        bulkActionsBar.classList.add('d-none');
    }
    const selectedCountSpan = document.getElementById('selectedCount');
    if (selectedCountSpan) {
        selectedCountSpan.textContent = '0';
    }
}

// Change password function
function changePassword(event, userId) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    fetch(`{{ url('admin/users') }}/${userId}/change-password`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            password: formData.get('password'),
            password_confirmation: formData.get('password_confirmation')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            bootstrap.Modal.getInstance(document.getElementById(`changePasswordModal_${userId}`)).hide();
            form.reset();
        } else {
            alert(data.message || 'حدث خطأ');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء تغيير كلمة المرور');
    });
}

// Toggle user status
function toggleUserStatus(userId) {
    fetch(`{{ url('admin/users') }}/${userId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'حدث خطأ');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ');
    });
}
</script>
