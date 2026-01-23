<!DOCTYPE html>
@include('admin.main.html')

<head>
    <title>إضافة صلاحية جديدة - أرشيف القاهرة الجديدة</title>
    @include('admin.main.meta')
</head>

<body>
    <div class="wrapper">
        @include('admin.main.topbar')
        @include('admin.main.sidebar')
        <div class="content-page">
            <div class="container-fluid">
                {{-- Header --}}
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-flex align-items-center justify-content-between">
                            <h4 class="mb-0">
                                <i class="ti ti-shield-plus me-2"></i>إضافة صلاحية جديدة
                            </h4>
                            <div class="page-title-right">
                                <ol class="m-0 breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">الصلاحيات</a></li>
                                    <li class="breadcrumb-item active">إضافة صلاحية</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.roles.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        {{-- Role Name Card --}}
                        <div class="col-lg-4">
                            <div class="border-0 shadow card">
                                <div class="card-header">
                                    <h5 class="mb-0 card-title"><i class="ti ti-info-circle me-2"></i>بيانات الصلاحية</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">اسم الصلاحية <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                               value="{{ old('name') }}" placeholder="مثال: محرر، مشرف، مدير..." required>
                                        @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Actions Card --}}
                            <div class="border-0 shadow card">
                                <div class="card-header">
                                    <h5 class="mb-0 card-title"><i class="ti ti-settings me-2"></i>الإجراءات</h5>
                                </div>
                                <div class="card-body">
                                    <div class="gap-2 d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-check me-1"></i>حفظ الصلاحية
                                        </button>
                                        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                                            <i class="ti ti-arrow-right me-1"></i>العودة للقائمة
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Quick Actions --}}
                            <div class="border-0 shadow card">
                                <div class="card-header">
                                    <h5 class="mb-0 card-title"><i class="ti ti-bolt me-2"></i>إجراءات سريعة</h5>
                                </div>
                                <div class="card-body">
                                    <div class="gap-2 d-grid">
                                        <button type="button" class="btn btn-outline-primary" onclick="selectAllPerms()">
                                            <i class="ti ti-checks me-1"></i>تحديد الكل
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="deselectAllPerms()">
                                            <i class="ti ti-x me-1"></i>إلغاء تحديد الكل
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Permissions Card --}}
                        <div class="col-lg-8">
                            <div class="border-0 shadow card">
                                <div class="card-header">
                                    <h5 class="mb-0 card-title"><i class="ti ti-key me-2"></i>الأذونات</h5>
                                </div>
                                <div class="card-body">
                                    <div class="accordion" id="permissionsAccordion">
                                        @foreach($permissions as $module => $modulePermissions)
                                        @php
                                            $moduleNameAr = $moduleTranslations[$module] ?? $module;
                                        @endphp
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#module_{{ Str::slug($module) }}">
                                                    <div class="d-flex align-items-center w-100">
                                                        <span class="flex-grow-1">
                                                            <i class="ti ti-folder me-2"></i>{{ $moduleNameAr }}
                                                            <span class="badge bg-secondary ms-2">{{ $modulePermissions->count() }}</span>
                                                        </span>
                                                        <div class="form-check form-switch me-3" onclick="event.stopPropagation()">
                                                            <input type="checkbox" class="form-check-input module-toggle" data-module="{{ Str::slug($module) }}" onchange="toggleModule(this)">
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="module_{{ Str::slug($module) }}" class="accordion-collapse collapse" data-bs-parent="#permissionsAccordion">
                                                <div class="accordion-body">
                                                    <div class="row g-3">
                                                        @foreach($modulePermissions as $permission)
                                                        @php
                                                            // Dynamic translation: extract action from permission name
                                                            // Supports: clients.view -> view, clients.bulk-delete -> bulk-delete
                                                            $permName = $permission->name;
                                                            $action = '';
                                                            if (str_contains($permName, '.')) {
                                                                $parts = explode('.', $permName);
                                                                $action = $parts[1] ?? '';
                                                            }
                                                            $permNameAr = $actionTranslations[$action] ?? $action ?: $permName;
                                                        @endphp
                                                        <div class="col-md-6 col-lg-4">
                                                            <div class="p-2 border form-check rounded-2 bg-light-subtle">
                                                                <input type="checkbox" class="form-check-input perm-checkbox module-{{ Str::slug($module) }}"
                                                                       name="permissions[]" value="{{ $permission->name }}"
                                                                       id="perm_{{ $permission->id }}"
                                                                       {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                                    {{ $permNameAr }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('admin.main.scripts')

    <script>
    function toggleModule(toggle) {
        const moduleSlug = toggle.dataset.module;
        const isChecked = toggle.checked;
        document.querySelectorAll(`.module-${moduleSlug}`).forEach(cb => cb.checked = isChecked);
    }

    function selectAllPerms() {
        document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = true);
        document.querySelectorAll('.module-toggle').forEach(cb => cb.checked = true);
    }

    function deselectAllPerms() {
        document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = false);
        document.querySelectorAll('.module-toggle').forEach(cb => cb.checked = false);
    }

    // Update module toggle state when individual permissions change
    document.querySelectorAll('.perm-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            const classes = this.className.split(' ');
            const moduleClass = classes.find(c => c.startsWith('module-') && c !== 'module-toggle');
            if (moduleClass) {
                const moduleSlug = moduleClass.replace('module-', '');
                const allInModule = document.querySelectorAll(`.module-${moduleSlug}`);
                const checkedInModule = document.querySelectorAll(`.module-${moduleSlug}:checked`);
                const toggle = document.querySelector(`.module-toggle[data-module="${moduleSlug}"]`);
                if (toggle) {
                    toggle.checked = allInModule.length === checkedInModule.length;
                    toggle.indeterminate = checkedInModule.length > 0 && checkedInModule.length < allInModule.length;
                }
            }
        });
    });
    </script>
</body>
</html>
