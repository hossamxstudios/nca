<!-- Sidenav Menu Start -->
<div class="sidenav-menu">
    <div class="scrollbar" data-simplebar>
        <div class="p-2 border border-dashed sidenav-user rounded-3">
            <div class="d-flex align-items-center">
                <img src="{{ asset('logo.webp') }}" width="45" class="flex-shrink-0 rounded-circle me-2" alt="user-image">
                <div class="overflow-hidden flex-grow-1">
                    <h6 class="my-0 fw-semibold text-truncate">
                        {{ auth()->user()->first_name . ' ' . auth()->user()->last_name ?? 'مستخدم' }}
                    </h6>
                    <small class="text-muted">{{ auth()->user()->roles->first()->name ?? 'مدير' }}</small>
                </div>
            </div>
        </div>

        <!--- Sidenav Menu -->
        <ul class="side-nav">
            @can('users.view')
            <!-- Dashboard -->
            <li class="side-nav-item">
                <a href="{{ route('dashboard') }}" class="side-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="menu-icon"><i class="ti ti-dashboard"></i></span>
                    <span class="menu-text">لوحة التحكم</span>
                </a>
            </li>
            @endcan

            <!-- ==================== Archive System Module ==================== -->
            <li class="mt-2 side-nav-title">نظام الأرشيف</li>

            <!-- Archive Clients -->
            @can('clients.view')
                <li class="side-nav-item">
                    <a href="{{ route('admin.clients.index') }}" class="side-nav-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i class="ti ti-users"></i></span>
                        <span class="menu-text">إدارة العملاء</span>
                    </a>
                </li>
            @endcan

            <!-- Archive Lands -->
            {{-- @can('lands.view')
                <li class="side-nav-item">
                    <a href="{{ route('admin.lands.index') }}"
                        class="side-nav-link {{ request()->routeIs('admin.lands.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i class="ti ti-map-2"></i></span>
                        <span class="menu-text">إدارة الأراضي</span>
                    </a>
                </li>
            @endcan --}}


            <!-- ==================== System Settings ==================== -->

            <!-- Users Management -->
            @can('users.view')
                <li class="mt-3 side-nav-title">ادارة معلومات المستخدمين والصلاحيات</li>
                <li class="side-nav-item">
                    <a href="{{ route('admin.users.index') }}" class="side-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i class="ti ti-user-cog"></i></span>
                        <span class="menu-text">إدارة المستخدمين</span>
                    </a>
                </li>
            @endcan

            <!-- Roles & Permissions -->
            @can('roles.view')
                <li class="side-nav-item">
                    <a href="{{ route('admin.roles.index') }}" class="side-nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i class="ti ti-shield-lock"></i></span>
                        <span class="menu-text">الأدوار والصلاحيات</span>
                    </a>
                </li>
            @endcan
            @canany(['physical_locations.view', 'geographic_areas.view', 'items.view'])

                <!-- Physical Locations -->
                <li class="mt-3 side-nav-title">ادارة معلومات النظام الأساسية</li>

                <!-- Manage System Basics -->
                <li class="side-nav-item has-sub-menu">
                    <a href="javascript:void(0);" class="side-nav-link" onclick="toggleSystemBasicsMenu(event)"
                        role="button" aria-expanded="false" aria-controls="sub-menu-system-basics">
                        <span class="menu-icon"><i class="ti ti-settings"></i></span>
                        <span class="menu-text">ادارة معلومات النظام</span>
                        <span class="menu-icon-close">
                            <i class="ti ti-chevron-down small"></i>
                        </span>
                    </a>
                    <ul class="sub-menu sub-menu-animated" id="sub-menu-system-basics">
                        <!-- Physical Locations -->
                        @can('physical_locations.view')
                            <li class="side-nav-item">
                                <a href="{{ route('admin.physical-locations.index') }}" class="side-nav-link {{ request()->routeIs('admin.physical-locations.*') ? 'active' : '' }}">
                                    <span class="menu-icon"><i class="ti ti-building-warehouse"></i></span>
                                    <span class="menu-text">مواقع التخزين</span>
                                </a>
                            </li>
                        @endcan

                        <!-- Geographic Areas -->
                        @can('geographic_areas.view')
                            <li class="side-nav-item">
                                <a href="{{ route('admin.geographic-areas.index') }}" class="side-nav-link {{ request()->routeIs('admin.geographic-areas.*') ? 'active' : '' }}">
                                    <span class="menu-icon"><i class="ti ti-map-pin"></i></span>
                                    <span class="menu-text">المناطق الجغرافية</span>
                                </a>
                            </li>
                        @endcan

                        <!-- Content Types (Items) -->
                        @can('items.view')
                            <li class="side-nav-item">
                                <a href="{{ route('admin.items.index') }}" class="side-nav-link {{ request()->routeIs('admin.items.*') ? 'active' : '' }}">
                                    <span class="menu-icon"><i class="ti ti-tags"></i></span>
                                    <span class="menu-text">أنواع المحتوى</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            <!-- Import Data -->
            @can('import.access')
                <li class="mt-3 side-nav-title">ادارة معلومات استيراد البيانات</li>
                <li class="side-nav-item">
                    <a href="{{ route('admin.imports.index') }}" class="side-nav-link {{ request()->routeIs('admin.imports.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i class="ti ti-file-import"></i></span>
                        <span class="menu-text">استيراد البيانات</span>
                    </a>
                </li>
            @endcan

        </ul>
    </div>
</div>
<!-- Sidenav Menu End -->

<style>
    /* Sidebar submenu animation */
    .sub-menu-animated {
        overflow: hidden;
        max-height: 0;
        opacity: 0;
        transition: max-height 0.3s ease-out, opacity 0.25s ease-out, padding 0.3s ease-out;
        padding-top: 0;
        padding-bottom: 0;
    }

    .sub-menu-animated.open {
        max-height: 500px;
        opacity: 1;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        transition: max-height 0.4s ease-in, opacity 0.3s ease-in, padding 0.3s ease-in;
    }

    /* Chevron rotation animation */
    .has-sub-menu .menu-icon-close,
    .has-sub-menu .menu-icon-open {
        transition: transform 0.3s ease;
    }

    .has-sub-menu.open .menu-icon-close {
        transform: rotate(180deg);
    }
</style>

<script>
    // Toggle system basics submenu with animation
    function toggleSystemBasicsMenu(e) {
        e.preventDefault();
        e.stopPropagation();

        const submenu = document.getElementById('sub-menu-system-basics');
        const link = e.currentTarget;
        const parentLi = link.closest('.has-sub-menu');

        if (!submenu.classList.contains('open')) {
            submenu.classList.add('open');
            link.setAttribute('aria-expanded', 'true');
            parentLi?.classList.add('open');
        } else {
            submenu.classList.remove('open');
            link.setAttribute('aria-expanded', 'false');
            parentLi?.classList.remove('open');
        }
    }

    // Auto-expand submenu if child is active
    document.addEventListener('DOMContentLoaded', function() {
        const submenu = document.getElementById('sub-menu-system-basics');
        if (submenu) {
            // Add animation class
            submenu.classList.add('sub-menu-animated');

            const activeChild = submenu.querySelector('.side-nav-link.active');
            if (activeChild) {
                submenu.classList.add('open');
                const link = document.querySelector('[onclick="toggleSystemBasicsMenu(event)"]');
                if (link) {
                    link.setAttribute('aria-expanded', 'true');
                    link.closest('.has-sub-menu')?.classList.add('open');
                }
            }
        }
    });
</script>
