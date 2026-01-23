<!-- Topbar Start -->
<header class="app-topbar">
    <div class="container-fluid topbar-menu">
        <div class="gap-2 d-flex align-items-center justify-content-center">
            <!-- Topbar Brand Logo -->
            <div class="logo-topbar">
                <a href="{{ route('dashboard') }}" class="logo-dark">
                    <span class="gap-1 d-flex align-items-center">
                        {{-- <span class="avatar avatar-xs rounded-circle text-bg-dark">
                            <span class="avatar-title">
                                <img src="{{ asset('logo.jpg') }}" height="50" alt="Logo">
                            </span>
                        </span> --}}
                        <span class="logo-text text-body fw-bold fs-xl">أرشيف القاهرة الجديدة</span>
                    </span>
                </a>
                <a href="{{ route('dashboard') }}" class="logo-light">
                    <span class="gap-1 d-flex align-items-center">
                        <span class="avatar avatar-xs rounded-circle text-bg-dark">
                            <span class="avatar-title">
                                <i data-lucide="sparkles" class="fs-md"></i>
                            </span>
                        </span>
                        <span class="text-white logo-text fw-bold fs-xl">أرشيف القاهرة الجديدة</span>
                    </span>
                </a>
            </div>

            <div class="mx-1 d-lg-none d-flex">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('logo.webp') }}" height="28" alt="Logo">
                </a>
            </div>

            <!-- Sidebar Hover Menu Toggle Button -->
            <button class="button-collapse-toggle d-xl-none">
                <i data-lucide="menu" class="align-middle fs-22"></i>
            </button>
        </div>

        <div class="gap-2 d-flex align-items-center">
            <!-- User Dropdown -->
            <div class="topbar-item nav-user">
                <div class="dropdown">
                    <a class="px-2 topbar-link dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown"
                        data-bs-offset="0,13" href="#!" aria-haspopup="false" aria-expanded="false">
                        @php
                            try {
                                $userAvatar = method_exists(Auth::user(), 'getFirstMediaUrl') ? Auth::user()->getFirstMediaUrl('avatar') : null;
                            } catch (\Exception $e) {
                                $userAvatar = null;
                            }
                            $userInitial = !empty(Auth::user()->first_name) ? strtoupper(substr(Auth::user()->first_name, 0, 1)) : 'م';
                        @endphp
                        @if($userAvatar)
                            <img src="{{ $userAvatar }}" width="40" height="40"
                                class="rounded-circle d-flex" alt="user-image" style="object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <span class="fw-bold">{{ $userInitial }}</span>
                            </div>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- Header -->
                        <div class="dropdown-header noti-title">
                            <h6 class="m-0 text-overflow">مرحباً بك!</h6>
                            <small class="text-muted">{{ Auth::user()->first_name . ' ' . Auth::user()->last_name }}</small>
                        </div>

                        <!-- My Profile -->
                        <a href="{{ route('admin.profile.index') }}" class="dropdown-item">
                            <i class="align-middle ti ti-user-circle me-2 fs-17"></i>
                            <span class="align-middle">الملف الشخصي</span>
                        </a>

                        <!-- Divider -->
                        <div class="dropdown-divider"></div>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger fw-semibold">
                                <i class="align-middle ti ti-logout-2 me-2 fs-17"></i>
                                <span class="align-middle">تسجيل الخروج</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- Topbar End -->
