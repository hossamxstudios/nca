<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        try {
            DB::beginTransaction();

            // Reset cached roles and permissions
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            // Define permissions by module with Arabic translations
            $permissions = [
                // Client Management
                'clients.view' => 'عرض العملاء',
                'clients.create' => 'إضافة عميل',
                'clients.edit' => 'تعديل عميل',
                'clients.delete' => 'حذف عميل',
                'clients.restore' => 'استعادة عميل',
                'clients.force-delete' => 'حذف نهائي للعميل',
                'clients.export' => 'تصدير العملاء',
                'clients.print' => 'طباعة بيانات العملاء',
                'clients.bulk-delete' => 'حذف مجموعة عملاء',
                'clients.bulk-restore' => 'استعادة مجموعة عملاء',
                'clients.bulk-force-delete' => 'حذف نهائي لمجموعة عملاء',
                'clients.bulk-print' => 'طباعة مجموعة عملاء',

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

                // Geographic Areas
                'geographic_areas.view' => 'عرض المناطق الجغرافية',
                'geographic_areas.create' => 'إضافة منطقة جغرافية',
                'geographic_areas.edit' => 'تعديل منطقة جغرافية',
                'geographic_areas.delete' => 'حذف منطقة جغرافية',

                // Items (Content Types)
                'items.view' => 'عرض أنواع المحتوى',
                'items.create' => 'إضافة نوع محتوى',
                'items.edit' => 'تعديل نوع محتوى',
                'items.delete' => 'حذف نوع محتوى',

                // Import
                'import.access' => 'الوصول للاستيراد',
                'import.view' => 'عرض عمليات الاستيراد',
                'import.execute' => 'تنفيذ الاستيراد',
                'import.delete' => 'حذف عملية استيراد',
                'import.download-template' => 'تحميل قالب الاستيراد',

                // User Management
                'users.view' => 'عرض المستخدمين',
                'users.create' => 'إضافة مستخدم',
                'users.edit' => 'تعديل مستخدم',
                'users.delete' => 'حذف مستخدم',
                'users.restore' => 'استعادة مستخدم',
                'users.force-delete' => 'حذف نهائي للمستخدم',
                'users.bulk-delete' => 'حذف مجموعة مستخدمين',
                'users.bulk-restore' => 'استعادة مجموعة مستخدمين',
                'users.bulk-force-delete' => 'حذف نهائي لمجموعة مستخدمين',
                'users.toggle-status' => 'تغيير حالة المستخدم',
                'users.assign-role' => 'تعيين صلاحية للمستخدم',
                'users.change-password' => 'تغيير كلمة مرور المستخدم',

                // Roles & Permissions
                'roles.view' => 'عرض الأدوار',
                'roles.create' => 'إضافة دور',
                'roles.edit' => 'تعديل دور',
                'roles.delete' => 'حذف دور',
                'roles.bulk-delete' => 'حذف مجموعة أدوار',
                'roles.sync-permissions' => 'مزامنة الأذونات',

                // Reports
                'reports.view' => 'عرض التقارير',
                'reports.export' => 'تصدير التقارير',
                'reports.create' => 'إنشاء تقرير',

                // Activity Logs
                'activity-logs.view' => 'عرض سجل النشاطات',
                'activity-logs.delete' => 'حذف سجل النشاطات',
                'activity-logs.export' => 'تصدير سجل النشاطات',
            ];

            // Create permissions
            foreach ($permissions as $name => $description) {
                Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => 'web']
                );
            }

            // Define roles with their permissions
            $roles = [
                'Super Admin' => array_keys($permissions), // All permissions

                'Viewer' => [
                    'clients.view'
                ],
            ];

            // Create roles and assign permissions
            foreach ($roles as $roleName => $rolePermissions) {
                $role = Role::firstOrCreate(
                    ['name' => $roleName, 'guard_name' => 'web']
                );
                $role->syncPermissions($rolePermissions);
            }

            DB::commit();

            $this->command->info('Permissions and roles seeded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PermissionSeeder failed: ' . $e->getMessage());
            $this->command->error('Failed to seed permissions: ' . $e->getMessage());
        }
    }
}
