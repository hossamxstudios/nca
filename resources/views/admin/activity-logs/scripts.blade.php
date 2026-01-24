<script>
document.addEventListener('DOMContentLoaded', function() {
    // View details
    document.querySelectorAll('.btn-view-details').forEach(btn => {
        btn.addEventListener('click', function() {
            const logId = this.dataset.logId;
            const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
            const content = document.getElementById('detailsContent');

            content.innerHTML = '<div class="py-4 text-center"><div class="spinner-border text-primary" role="status"></div></div>';
            modal.show();

            fetch(`{{ url('activity-logs') }}/${logId}`)
                .then(res => res.json())
                .then(data => {
                    let changesHtml = '';

                    if (data.old_values || data.new_values) {
                        changesHtml = `
                            <div class="mt-3">
                                <h6 class="mb-2"><i class="ti ti-git-compare me-1"></i>التغييرات</h6>
                                <div class="table-responsive">
                                    <table class="table mb-0 table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>الحقل</th>
                                                <th>القيمة القديمة</th>
                                                <th>القيمة الجديدة</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                        `;

                        const allKeys = new Set([
                            ...Object.keys(data.old_values || {}),
                            ...Object.keys(data.new_values || {})
                        ]);

                        allKeys.forEach(key => {
                            const oldVal = data.old_values?.[key] ?? '-';
                            const newVal = data.new_values?.[key] ?? '-';
                            if (oldVal !== newVal) {
                                changesHtml += `
                                    <tr>
                                        <td><strong>${key}</strong></td>
                                        <td class="text-danger">${oldVal}</td>
                                        <td class="text-success">${newVal}</td>
                                    </tr>
                                `;
                            }
                        });

                        changesHtml += '</tbody></table></div></div>';
                    }

                    let affectedIdsHtml = '';
                    if (data.affected_ids && data.affected_ids.length > 0) {
                        affectedIdsHtml = `
                            <div class="mt-3">
                                <h6 class="mb-2"><i class="ti ti-list-numbers me-1"></i>العناصر المتأثرة (${data.affected_ids.length})</h6>
                                <div class="p-2 rounded bg-light" style="max-height: 150px; overflow-y: auto;">
                                    <code>${data.affected_ids.join(', ')}</code>
                                </div>
                            </div>
                        `;
                    }

                    content.innerHTML = `
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary mb-3" style="width: 64px; height: 64px;">
                                <i class="ti ${data.action_type_icon} text-white" style="font-size: 28px;"></i>
                            </div>
                            <h4 class="mb-1 fw-bold">${data.action_type}</h4>
                            <span class="text-muted">${data.action_group}</span>
                        </div>

                        <!-- Time -->
                        <div class="text-center mb-4 p-3 bg-light rounded-3">
                            <div style="font-size: 32px; font-weight: 700; font-family: 'SF Mono', 'Consolas', monospace; color: #333;">
                                ${data.created_at.split(' ')[1]}
                            </div>
                            <div class="text-muted">${data.created_at.split(' ')[0]}</div>
                            <small class="text-muted">${data.created_at_human}</small>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <div class="fw-semibold mb-2"><i class="ti ti-file-description me-2"></i>الوصف</div>
                            <div class="p-3 border rounded-3 bg-white">${data.description}</div>
                            ${data.subject_name ? `<div class="mt-2"><span class="badge bg-primary-subtle text-primary"><i class="ti ti-tag me-1"></i>${data.subject_name}</span></div>` : ''}
                        </div>

                        <!-- User & IP -->
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="fw-semibold mb-2"><i class="ti ti-user me-2"></i>المستخدم</div>
                                <div class="p-3 border rounded-3 bg-white">
                                    <div class="fw-medium">${data.user}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="fw-semibold mb-2"><i class="ti ti-world me-2"></i>عنوان IP</div>
                                <div class="p-3 border rounded-3 bg-white">
                                    <code>${data.ip_address || '-'}</code>
                                </div>
                            </div>
                        </div>

                        <!-- User Agent -->
                        <div class="mb-4">
                            <div class="fw-semibold mb-2"><i class="ti ti-device-desktop me-2"></i>المتصفح</div>
                            <div class="p-3 border rounded-3 bg-white small text-muted text-break">${data.user_agent || '-'}</div>
                        </div>

                        ${changesHtml}
                        ${affectedIdsHtml}
                    `;
                })
                .catch(err => {
                    content.innerHTML = '<div class="py-4 text-center text-danger"><i class="ti ti-alert-circle fs-1"></i><p>حدث خطأ في تحميل البيانات</p></div>';
                });
        });
    });
});
</script>
