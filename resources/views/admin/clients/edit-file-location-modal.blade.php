@foreach($client->files as $file)
<div class="modal fade" id="editFileLocationModal_{{ $file->id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle">
                <h5 class="modal-title">
                    <i class="ti ti-settings me-2"></i>تعديل موقع الملف
                    <span class="badge bg-primary ms-2">{{ $file->file_name }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form action="{{ route('admin.files.update-location', $file->id) }}" method="POST" class="edit-file-location-form" data-file-id="{{ $file->id }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="accordion" id="editLocationAccordion_{{ $file->id }}">
                        {{-- Geolocation Section --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="py-2 accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#editGeoCollapse_{{ $file->id }}">
                                    <i class="ti ti-map-pin me-2 text-primary"></i>
                                    <span class="fw-bold">العنوان الجغرافي (الأرض)</span>
                                </button>
                            </h2>
                            <div id="editGeoCollapse_{{ $file->id }}" class="accordion-collapse collapse show">
                                <div class="py-3 accordion-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">الحي <span class="text-danger">*</span></label>
                                            <select class="form-select edit-loc-district" name="district_id" required data-file="{{ $file->id }}" data-current="{{ $file->land?->district_id }}">
                                                <option value="">اختر الحي</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">المجاورة <span class="text-danger">*</span></label>
                                            <select class="form-select edit-loc-zone" name="zone_id" required data-file="{{ $file->id }}" data-current="{{ $file->land?->zone_id }}">
                                                <option value="">اختر المجاورة</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">المنطقة</label>
                                            <select class="form-select edit-loc-area" name="area_id" data-file="{{ $file->id }}" data-current="{{ $file->land?->area_id }}">
                                                <option value="">اختر المنطقة</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">رقم القطعة <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="land_no" required value="{{ $file->land?->land_no }}" placeholder="رقم القطعة">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Physical Location Section --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="py-2 accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#editPhysicalCollapse_{{ $file->id }}">
                                    <i class="ti ti-building me-2 text-info"></i>
                                    <span class="fw-bold">الموقع الفعلي</span>
                                    <span class="badge bg-secondary ms-2">اختياري</span>
                                </button>
                            </h2>
                            <div id="editPhysicalCollapse_{{ $file->id }}" class="accordion-collapse collapse show">
                                <div class="py-3 accordion-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">الغرفة</label>
                                            <select class="form-select edit-loc-room" name="room_id" data-file="{{ $file->id }}" data-current="{{ $file->room_id }}">
                                                <option value="">اختر الغرفة</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">الممر</label>
                                            <select class="form-select edit-loc-lane" name="lane_id" data-file="{{ $file->id }}" data-current="{{ $file->lane_id }}">
                                                <option value="">اختر الممر</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">الاستاند</label>
                                            <select class="form-select edit-loc-stand" name="stand_id" data-file="{{ $file->id }}" data-current="{{ $file->stand_id }}">
                                                <option value="">اختر الاستاند</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">الرف</label>
                                            <select class="form-select edit-loc-rack" name="rack_id" data-file="{{ $file->id }}" data-current="{{ $file->rack_id }}">
                                                <option value="">اختر الرف</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load data on modal show
    document.querySelectorAll('[id^="editFileLocationModal_"]').forEach(modal => {
        modal.addEventListener('show.bs.modal', function() {
            const fileId = this.id.replace('editFileLocationModal_', '');
            loadEditLocData(fileId);
        });
    });

    // District change -> load zones
    document.querySelectorAll('.edit-loc-district').forEach(select => {
        select.addEventListener('change', function() {
            const fileId = this.dataset.file;
            const districtId = this.value;
            const zoneSelect = document.querySelector(`.edit-loc-zone[data-file="${fileId}"]`);
            const areaSelect = document.querySelector(`.edit-loc-area[data-file="${fileId}"]`);

            zoneSelect.innerHTML = '<option value="">اختر المجاورة</option>';
            areaSelect.innerHTML = '<option value="">اختر المنطقة</option>';

            if (districtId) {
                fetch(`/api/zones/${districtId}`).then(r => r.json()).then(data => {
                    data.forEach(item => zoneSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`);
                });
            }
        });
    });

    // Zone change -> load areas
    document.querySelectorAll('.edit-loc-zone').forEach(select => {
        select.addEventListener('change', function() {
            const fileId = this.dataset.file;
            const zoneId = this.value;
            const areaSelect = document.querySelector(`.edit-loc-area[data-file="${fileId}"]`);

            areaSelect.innerHTML = '<option value="">اختر المنطقة</option>';

            if (zoneId) {
                fetch(`/api/areas/${zoneId}`).then(r => r.json()).then(data => {
                    data.forEach(item => areaSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`);
                });
            }
        });
    });

    // Room change -> load lanes
    document.querySelectorAll('.edit-loc-room').forEach(select => {
        select.addEventListener('change', function() {
            const fileId = this.dataset.file;
            const roomId = this.value;
            const laneSelect = document.querySelector(`.edit-loc-lane[data-file="${fileId}"]`);
            const standSelect = document.querySelector(`.edit-loc-stand[data-file="${fileId}"]`);
            const rackSelect = document.querySelector(`.edit-loc-rack[data-file="${fileId}"]`);

            laneSelect.innerHTML = '<option value="">اختر الممر</option>';
            standSelect.innerHTML = '<option value="">اختر الاستاند</option>';
            rackSelect.innerHTML = '<option value="">اختر الرف</option>';

            if (roomId) {
                fetch(`/api/lanes/${roomId}`).then(r => r.json()).then(data => {
                    data.forEach(item => laneSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`);
                });
            }
        });
    });

    // Lane change -> load stands
    document.querySelectorAll('.edit-loc-lane').forEach(select => {
        select.addEventListener('change', function() {
            const fileId = this.dataset.file;
            const laneId = this.value;
            const standSelect = document.querySelector(`.edit-loc-stand[data-file="${fileId}"]`);
            const rackSelect = document.querySelector(`.edit-loc-rack[data-file="${fileId}"]`);

            standSelect.innerHTML = '<option value="">اختر الاستاند</option>';
            rackSelect.innerHTML = '<option value="">اختر الرف</option>';

            if (laneId) {
                fetch(`/api/stands/${laneId}`).then(r => r.json()).then(data => {
                    data.forEach(item => standSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`);
                });
            }
        });
    });

    // Stand change -> load racks
    document.querySelectorAll('.edit-loc-stand').forEach(select => {
        select.addEventListener('change', function() {
            const fileId = this.dataset.file;
            const standId = this.value;
            const rackSelect = document.querySelector(`.edit-loc-rack[data-file="${fileId}"]`);

            rackSelect.innerHTML = '<option value="">اختر الرف</option>';

            if (standId) {
                fetch(`/api/racks/${standId}`).then(r => r.json()).then(data => {
                    data.forEach(item => rackSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`);
                });
            }
        });
    });

    async function loadEditLocData(fileId) {
        const districtSelect = document.querySelector(`.edit-loc-district[data-file="${fileId}"]`);
        const zoneSelect = document.querySelector(`.edit-loc-zone[data-file="${fileId}"]`);
        const areaSelect = document.querySelector(`.edit-loc-area[data-file="${fileId}"]`);
        const roomSelect = document.querySelector(`.edit-loc-room[data-file="${fileId}"]`);
        const laneSelect = document.querySelector(`.edit-loc-lane[data-file="${fileId}"]`);
        const standSelect = document.querySelector(`.edit-loc-stand[data-file="${fileId}"]`);
        const rackSelect = document.querySelector(`.edit-loc-rack[data-file="${fileId}"]`);

        const currentDistrict = districtSelect.dataset.current;
        const currentZone = zoneSelect.dataset.current;
        const currentArea = areaSelect.dataset.current;
        const currentRoom = roomSelect.dataset.current;
        const currentLane = laneSelect.dataset.current;
        const currentStand = standSelect.dataset.current;
        const currentRack = rackSelect.dataset.current;

        // Load districts
        const districts = await fetch('{{ route("admin.api.districts") }}').then(r => r.json());
        districtSelect.innerHTML = '<option value="">اختر الحي</option>';
        districts.forEach(item => {
            districtSelect.innerHTML += `<option value="${item.id}" ${item.id == currentDistrict ? 'selected' : ''}>${item.name}</option>`;
        });

        // Load zones if district selected
        if (currentDistrict) {
            const zones = await fetch(`/api/zones/${currentDistrict}`).then(r => r.json());
            zoneSelect.innerHTML = '<option value="">اختر المجاورة</option>';
            zones.forEach(item => {
                zoneSelect.innerHTML += `<option value="${item.id}" ${item.id == currentZone ? 'selected' : ''}>${item.name}</option>`;
            });
        }

        // Load areas if zone selected
        if (currentZone) {
            const areas = await fetch(`/api/areas/${currentZone}`).then(r => r.json());
            areaSelect.innerHTML = '<option value="">اختر المنطقة</option>';
            areas.forEach(item => {
                areaSelect.innerHTML += `<option value="${item.id}" ${item.id == currentArea ? 'selected' : ''}>${item.name}</option>`;
            });
        }

        // Load rooms
        const rooms = await fetch('{{ route("admin.api.rooms") }}').then(r => r.json());
        roomSelect.innerHTML = '<option value="">اختر الغرفة</option>';
        rooms.forEach(item => {
            roomSelect.innerHTML += `<option value="${item.id}" ${item.id == currentRoom ? 'selected' : ''}>${item.building_name} - ${item.name}</option>`;
        });

        // Load lanes if room selected
        if (currentRoom) {
            const lanes = await fetch(`/api/lanes/${currentRoom}`).then(r => r.json());
            laneSelect.innerHTML = '<option value="">اختر الممر</option>';
            lanes.forEach(item => {
                laneSelect.innerHTML += `<option value="${item.id}" ${item.id == currentLane ? 'selected' : ''}>${item.name}</option>`;
            });
        }

        // Load stands if lane selected
        if (currentLane) {
            const stands = await fetch(`/api/stands/${currentLane}`).then(r => r.json());
            standSelect.innerHTML = '<option value="">اختر الاستاند</option>';
            stands.forEach(item => {
                standSelect.innerHTML += `<option value="${item.id}" ${item.id == currentStand ? 'selected' : ''}>${item.name}</option>`;
            });
        }

        // Load racks if stand selected
        if (currentStand) {
            const racks = await fetch(`/api/racks/${currentStand}`).then(r => r.json());
            rackSelect.innerHTML = '<option value="">اختر الرف</option>';
            racks.forEach(item => {
                rackSelect.innerHTML += `<option value="${item.id}" ${item.id == currentRack ? 'selected' : ''}>${item.name}</option>`;
            });
        }
    }
});
</script>
