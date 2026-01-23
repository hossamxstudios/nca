<!DOCTYPE html>
@include('admin.main.html')
<head>
    <title>الملف الشخصي - أرشيف القاهرة الجديدة</title>
    @include('admin.main.meta')
</head>
<body>
    <div class="wrapper">
        @include('admin.main.topbar')
        @include('admin.main.sidebar')

        <div class="content-page">
            <div class="container-fluid">
                {{-- Page Header --}}
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <div class="page-title-right">
                                <ol class="m-0 breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
                                    <li class="breadcrumb-item active">الملف الشخصي</li>
                                </ol>
                            </div>
                            <h4 class="page-title">
                                <i class="ti ti-user-circle me-2"></i>الملف الشخصي
                            </h4>
                        </div>
                    </div>
                </div>

                {{-- Alerts --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ti ti-check me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ti ti-x me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    {{-- Profile Card --}}
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body text-center">
                                {{-- Avatar --}}
                                <div class="mb-3">
                                    @php
                                        $userAvatar = method_exists($user, 'getFirstMediaUrl') ? $user->getFirstMediaUrl('avatar') : null;
                                        $userInitial = !empty($user->first_name) ? mb_substr($user->first_name, 0, 1) : 'م';
                                    @endphp
                                    @if($userAvatar)
                                        <img src="{{ $userAvatar }}" class="rounded-circle img-thumbnail"
                                             width="120" height="120" style="object-fit: cover;" alt="avatar">
                                    @else
                                        <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center"
                                             style="width: 120px; height: 120px; font-size: 3rem;">
                                            <span class="fw-bold">{{ $userInitial }}</span>
                                        </div>
                                    @endif
                                </div>

                                <h4 class="mb-1">{{ $user->first_name }} {{ $user->last_name }}</h4>
                                <p class="text-muted mb-2">{{ $user->email }}</p>

                                @if($user->roles->count() > 0)
                                    <span class="badge bg-primary mb-3">{{ $user->roles->first()->name }}</span>
                                @endif

                                <div class="text-start mt-3">
                                    <p class="text-muted mb-2">
                                        <i class="ti ti-calendar me-2"></i>
                                        <strong>تاريخ الانضمام:</strong>
                                        {{ $user->created_at->format('Y-m-d') }}
                                    </p>
                                    @if($user->phone)
                                    <p class="text-muted mb-2">
                                        <i class="ti ti-phone me-2"></i>
                                        <strong>الهاتف:</strong>
                                        {{ $user->phone }}
                                    </p>
                                    @endif
                                </div>

                                {{-- Remove Avatar --}}
                                @if($userAvatar)
                                <form action="{{ route('admin.profile.avatar.remove') }}" method="POST" class="mt-3">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('هل تريد حذف الصورة الشخصية؟')">
                                        <i class="ti ti-trash me-1"></i>حذف الصورة
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Edit Forms --}}
                    <div class="col-lg-8">
                        {{-- Personal Info Form --}}
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 card-title">
                                    <i class="ti ti-edit me-2"></i>تعديل البيانات الشخصية
                                </h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">الاسم الأول <span class="text-danger">*</span></label>
                                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                                                   value="{{ old('first_name', $user->first_name) }}" required>
                                            @error('first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">اسم العائلة <span class="text-danger">*</span></label>
                                            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                                                   value="{{ old('last_name', $user->last_name) }}" required>
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                                   value="{{ old('email', $user->email) }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">رقم الهاتف</label>
                                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                                   value="{{ old('phone', $user->phone) }}">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">الصورة الشخصية</label>
                                        <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror"
                                               accept="image/*">
                                        <small class="text-muted">الصيغ المدعومة: JPG, PNG, GIF. الحد الأقصى: 2MB</small>
                                        @error('avatar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-check me-1"></i>حفظ التغييرات
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Change Password Form --}}
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 card-title">
                                    <i class="ti ti-lock me-2"></i>تغيير كلمة المرور
                                </h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.profile.password') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">كلمة المرور الحالية <span class="text-danger">*</span></label>
                                        <input type="password" name="current_password"
                                               class="form-control @error('current_password') is-invalid @enderror" required>
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">كلمة المرور الجديدة <span class="text-danger">*</span></label>
                                            <input type="password" name="password"
                                                   class="form-control @error('password') is-invalid @enderror" required>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                                            <input type="password" name="password_confirmation" class="form-control" required>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-warning">
                                        <i class="ti ti-key me-1"></i>تغيير كلمة المرور
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.main.scripts')
</body>
</html>
