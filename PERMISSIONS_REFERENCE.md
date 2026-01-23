# Permissions Reference Guide

## Permission Naming Convention

All permissions follow the **dot notation** format: `module.action`

Example: `users.view`, `clients.create`, `files.delete`

---

## Complete Permissions List

### Client Management
- `clients.view` - عرض العملاء
- `clients.create` - إضافة عميل
- `clients.edit` - تعديل عميل
- `clients.delete` - حذف عميل
- `clients.export` - تصدير العملاء

### Land Management
- `lands.view` - عرض القطع
- `lands.create` - إضافة قطعه
- `lands.edit` - تعديل قطعه
- `lands.delete` - حذف قطعه

### File Management
- `files.view` - عرض الملفات
- `files.upload` - رفع ملفات
- `files.delete` - حذف ملفات
- `files.download` - تحميل ملفات

### Physical Locations
- `physical_locations.view` - عرض مواقع التخزين
- `physical_locations.manage` - إدارة مواقع التخزين

### Geographic Areas
- `geographic_areas.view` - عرض المناطق الجغرافية
- `geographic_areas.manage` - إدارة المناطق الجغرافية

### Items (Content Types)
- `items.view` - عرض أنواع المحتوى
- `items.manage` - إدارة أنواع المحتوى

### Import
- `import.access` - الوصول للاستيراد
- `import.execute` - تنفيذ الاستيراد

### User Management
- `users.view` - عرض المستخدمين
- `users.create` - إضافة مستخدم
- `users.edit` - تعديل مستخدم
- `users.delete` - حذف مستخدم
- `users.restore` - استعادة مستخدم
- `users.force-delete` - حذف نهائي للمستخدم
- `users.bulk-upload` - استيراد مستخدمين
- `users.bulk-download` - تصدير مستخدمين
- `users.bulk-delete` - حذف مجموعة مستخدمين
- `users.bulk-restore` - استعادة مجموعة مستخدمين
- `users.bulk-force-delete` - حذف نهائي لمجموعة مستخدمين

### Roles & Permissions
- `roles.view` - عرض الأدوار
- `roles.create` - إضافة دور
- `roles.edit` - تعديل دور
- `roles.delete` - حذف دور
- `roles.manage` - إدارة الأدوار

### Reports
- `reports.view` - عرض التقارير
- `reports.export` - تصدير التقارير

---

## Role Permissions Matrix

### Super Admin
✅ **ALL PERMISSIONS** (39 permissions)

### Manager (15 permissions)
- All client operations (view, create, edit, delete, export)
- All land operations (view, create, edit, delete)
- All file operations (view, upload, delete, download)
- All physical locations (view, manage)
- All geographic areas (view, manage)
- All items (view, manage)
- Import access and execute
- User management (view, create, edit, delete)
- View roles
- Reports (view, export)

### Employee (13 permissions)
- Clients: view, create, edit
- Lands: view, create, edit
- Files: view, upload, download
- Physical locations: view
- Geographic areas: view
- Items: view
- Import: access
- Users: view
- Reports: view

### Viewer (8 permissions)
- Clients: view
- Lands: view
- Files: view, download
- Physical locations: view
- Geographic areas: view
- Items: view
- Reports: view

---

## Usage in Views

Use `@can()` directive with dot notation:

```blade
@can('users.create')
    <button>إضافة مستخدم</button>
@endcan

@can('clients.edit')
    <a href="{{ route('admin.clients.edit', $client) }}">تعديل</a>
@endcan

@can('files.delete')
    <button onclick="deleteFile({{ $file->id }})">حذف</button>
@endcan
```

---

## Usage in Controllers

Use middleware or authorize methods:

```php
// In constructor
public function __construct()
{
    $this->middleware('permission:users.view')->only(['index', 'show']);
    $this->middleware('permission:users.create')->only(['create', 'store']);
    $this->middleware('permission:users.edit')->only(['edit', 'update']);
    $this->middleware('permission:users.delete')->only(['destroy']);
}

// Or in methods
public function store(Request $request)
{
    $this->authorize('users.create');
    // ... your code
}
```

---

## Important Notes

1. **Consistency**: Always use dot notation (module.action)
2. **Super Admin**: Has ALL permissions automatically
3. **Protected Roles**: Super Admin and Admin roles cannot be edited/deleted
4. **Self-Protection**: Users cannot delete/deactivate their own account
5. **Soft Deletes**: User model uses SoftDeletes trait
6. **Cache**: Run `php artisan permission:cache-reset` after permission changes

---

## Commands

```bash
# Seed permissions
php artisan db:seed --class=PermissionSeeder

# Clear permission cache
php artisan permission:cache-reset

# Show all permissions
php artisan permission:show

# Clear all caches
php artisan cache:clear
```
