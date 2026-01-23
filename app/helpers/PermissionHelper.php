<?php

namespace App\Helpers;

class PermissionHelper
{
    /**
     * Get Arabic translation for permission name
     *
     * @param string $permission
     * @return string
     */
    public static function translatePermission(string $permission): string
    {
        $translations = [
            // Client Management
            'clients.view' => 'عرض العملاء',
            'clients.create' => 'إضافة عميل',
            'clients.edit' => 'تعديل عميل',
            'clients.delete' => 'حذف عميل',
            'clients.restore' => 'استعادة عميل',
            'clients.force-delete' => 'حذف نهائي للعميل',
            'clients.export' => 'تصدير العملاء',
            'clients.bulk-upload' => 'استيراد عملاء',
            'clients.bulk-download' => 'تصدير عملاء',
            'clients.bulk-delete' => 'حذف مجموعة عملاء',
            'clients.bulk-restore' => 'استعادة مجموعة عملاء',
            'clients.bulk-force-delete' => 'حذف نهائي لمجموعة عملاء',

            // Land Management
            'lands.view' => 'عرض القطع',
            'lands.create' => 'إضافة قطعة',
            'lands.edit' => 'تعديل قطعة',
            'lands.delete' => 'حذف قطعة',
            'lands.restore' => 'استعادة قطعة',
            'lands.force-delete' => 'حذف نهائي للقطعة',

            // File Management
            'files.view' => 'عرض الملفات',
            'files.create' => 'إضافة ملف',
            'files.upload' => 'رفع ملفات',
            'files.edit' => 'تعديل ملف',
            'files.delete' => 'حذف ملفات',
            'files.download' => 'تحميل ملفات',

            // Physical Locations
            'physical_locations.view' => 'عرض مواقع التخزين',
            'physical_locations.create' => 'إضافة موقع تخزين',
            'physical_locations.edit' => 'تعديل موقع تخزين',
            'physical_locations.delete' => 'حذف موقع تخزين',
            'physical_locations.manage' => 'إدارة مواقع التخزين',

            // Geographic Areas
            'geographic_areas.view' => 'عرض المناطق الجغرافية',
            'geographic_areas.create' => 'إضافة منطقة جغرافية',
            'geographic_areas.edit' => 'تعديل منطقة جغرافية',
            'geographic_areas.delete' => 'حذف منطقة جغرافية',
            'geographic_areas.manage' => 'إدارة المناطق الجغرافية',
            'geographic-areas.view' => 'عرض المناطق الجغرافية',
            'geographic-areas.create' => 'إضافة منطقة جغرافية',
            'geographic-areas.edit' => 'تعديل منطقة جغرافية',
            'geographic-areas.delete' => 'حذف منطقة جغرافية',
            'geographic-areas.manage' => 'إدارة المناطق الجغرافية',

            // Items (Content Types)
            'items.view' => 'عرض أنواع المحتوى',
            'items.create' => 'إضافة نوع محتوى',
            'items.edit' => 'تعديل نوع محتوى',
            'items.delete' => 'حذف نوع محتوى',
            'items.manage' => 'إدارة أنواع المحتوى',

            // Import
            'import.access' => 'الوصول للاستيراد',
            'import.view' => 'عرض عمليات الاستيراد',
            'import.execute' => 'تنفيذ الاستيراد',
            'import.delete' => 'حذف عملية استيراد',

            // User Management
            'users.view' => 'عرض المستخدمين',
            'users.create' => 'إضافة مستخدم',
            'users.edit' => 'تعديل مستخدم',
            'users.delete' => 'حذف مستخدم',
            'users.restore' => 'استعادة مستخدم',
            'users.force-delete' => 'حذف نهائي للمستخدم',
            'users.bulk-upload' => 'استيراد مستخدمين',
            'users.bulk-download' => 'تصدير مستخدمين',
            'users.bulk-delete' => 'حذف مجموعة مستخدمين',
            'users.bulk-restore' => 'استعادة مجموعة مستخدمين',
            'users.bulk-force-delete' => 'حذف نهائي لمجموعة مستخدمين',

            // Roles & Permissions
            'roles.view' => 'عرض الأدوار',
            'roles.create' => 'إضافة دور',
            'roles.edit' => 'تعديل دور',
            'roles.delete' => 'حذف دور',
            'roles.restore' => 'استعادة دور',
            'roles.force-delete' => 'حذف نهائي للدور',
            'roles.manage' => 'إدارة الأدوار',
            'roles.bulk-delete' => 'حذف مجموعة أدوار',
            'roles.bulk-download' => 'تصدير الأدوار',

            // Reports
            'reports.view' => 'عرض التقارير',
            'reports.export' => 'تصدير التقارير',
            'reports.create' => 'إنشاء تقرير',
        ];

        return $translations[$permission] ?? $permission;
    }

    /**
     * Get Arabic translation for module name
     *
     * @param string $module
     * @return string
     */
    public static function translateModule(string $module): string
    {
        $translations = [
            'clients' => 'العملاء',
            'lands' => 'القطع',
            'files' => 'الملفات',
            'physical_locations' => 'مواقع التخزين',
            'geographic_areas' => 'المناطق الجغرافية',
            'geographic-areas' => 'المناطق الجغرافية',
            'items' => 'أنواع المحتوى',
            'import' => 'الاستيراد',
            'users' => 'المستخدمين',
            'roles' => 'الأدوار',
            'reports' => 'التقارير',
        ];

        return $translations[$module] ?? $module;
    }
}
