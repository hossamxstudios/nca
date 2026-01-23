<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Navigation Configuration
    |--------------------------------------------------------------------------
    |
    | This file defines the navigation items for the admin sidebar.
    | Each item can have: title, icon, route, permission, children
    |
    */

    'items' => [
        [
            'title' => 'لوحة التحكم',
            'title_en' => 'Dashboard',
            'icon' => 'ti ti-dashboard',
            'route' => 'admin.dashboard',
            'permission' => null,
        ],
        [
            'title' => 'إدارة العملاء',
            'title_en' => 'Clients',
            'icon' => 'ti ti-users',
            'route' => 'admin.clients.index',
            'permission' => 'clients.view',
            'badge' => [
                'model' => \App\Models\Client::class,
                'color' => 'primary',
            ],
        ],
        [
            'title' => 'إدارة القطع',
            'title_en' => 'Lands',
            'icon' => 'ti ti-map-2',
            'route' => 'admin.lands.index',
            'permission' => 'lands.view',
        ],
        [
            'title' => 'إدارة الملفات',
            'title_en' => 'Files',
            'icon' => 'ti ti-files',
            'route' => 'admin.files.index',
            'permission' => 'files.view',
        ],
        [
            'title' => 'مواقع التخزين',
            'title_en' => 'Physical Locations',
            'icon' => 'ti ti-building-warehouse',
            'route' => 'admin.physical-locations.index',
            'permission' => 'physical_locations.view',
        ],
        [
            'title' => 'المناطق الجغرافية',
            'title_en' => 'Geographic Areas',
            'icon' => 'ti ti-map-pin',
            'route' => 'admin.geographic-areas.index',
            'permission' => 'geographic_areas.view',
        ],
        [
            'title' => 'أنواع المحتوى',
            'title_en' => 'Content Types',
            'icon' => 'ti ti-tags',
            'route' => 'admin.items.index',
            'permission' => 'items.view',
        ],
        [
            'title' => 'استيراد البيانات',
            'title_en' => 'Import Data',
            'icon' => 'ti ti-upload',
            'route' => 'admin.imports.index',
            'permission' => 'import.access',
        ],
        [
            'title' => 'إدارة المستخدمين',
            'title_en' => 'Users',
            'icon' => 'ti ti-user-cog',
            'route' => 'admin.users.index',
            'permission' => 'users.view',
        ],
        [
            'title' => 'الأدوار والصلاحيات',
            'title_en' => 'Roles & Permissions',
            'icon' => 'ti ti-shield-lock',
            'route' => 'admin.roles.index',
            'permission' => 'roles.view',
        ],
    ],
];
