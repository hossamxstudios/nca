<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action_type',
        'action_group',
        'subject_type',
        'subject_id',
        'subject_name',
        'description',
        'properties',
        'batch_id',
        'batch_count',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    // Action Types
    const ACTION_LOGIN = 'login';
    const ACTION_LOGOUT = 'logout';
    const ACTION_VIEW = 'view';
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_PRINT = 'print';
    const ACTION_BULK_IMPORT = 'bulk_import';
    const ACTION_BULK_DELETE = 'bulk_delete';
    const ACTION_EXPORT = 'export';
    const ACTION_DOWNLOAD = 'download';
    const ACTION_SEARCH = 'search';
    const ACTION_BACKUP = 'backup';

    // Action Groups
    const GROUP_AUTH = 'auth';
    const GROUP_CLIENTS = 'clients';
    const GROUP_FILES = 'files';
    const GROUP_USERS = 'users';
    const GROUP_ROLES = 'roles';
    const GROUP_SETTINGS = 'settings';
    const GROUP_IMPORTS = 'imports';
    const GROUP_GEOGRAPHIC = 'geographic';
    const GROUP_PHYSICAL = 'physical';
    const GROUP_BACKUP = 'backup';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes for filtering
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByActionType($query, $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    public function scopeByActionGroup($query, $actionGroup)
    {
        return $query->where('action_group', $actionGroup);
    }

    public function scopeByDateRange($query, $from, $to)
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('description', 'like', "%{$search}%")
              ->orWhere('subject_name', 'like', "%{$search}%");
        });
    }

    // Helper to get action type label in Arabic
    public function getActionTypeLabelAttribute(): string
    {
        return match ($this->action_type) {
            self::ACTION_LOGIN => 'تسجيل دخول',
            self::ACTION_LOGOUT => 'تسجيل خروج',
            self::ACTION_VIEW => 'عرض',
            self::ACTION_CREATE => 'إنشاء',
            self::ACTION_UPDATE => 'تعديل',
            self::ACTION_DELETE => 'حذف',
            self::ACTION_PRINT => 'طباعة',
            self::ACTION_BULK_IMPORT => 'استيراد جماعي',
            self::ACTION_BULK_DELETE => 'حذف جماعي',
            self::ACTION_EXPORT => 'تصدير',
            self::ACTION_DOWNLOAD => 'تحميل',
            self::ACTION_SEARCH => 'بحث',
            self::ACTION_BACKUP => 'نسخ احتياطي',
            default => $this->action_type,
        };
    }

    // Helper to get action group label in Arabic
    public function getActionGroupLabelAttribute(): string
    {
        return match ($this->action_group) {
            self::GROUP_AUTH => 'المصادقة',
            self::GROUP_CLIENTS => 'العملاء',
            self::GROUP_FILES => 'الملفات',
            self::GROUP_USERS => 'المستخدمين',
            self::GROUP_ROLES => 'الصلاحيات',
            self::GROUP_SETTINGS => 'الإعدادات',
            self::GROUP_IMPORTS => 'الاستيراد',
            self::GROUP_GEOGRAPHIC => 'المواقع الجغرافية',
            self::GROUP_PHYSICAL => 'المواقع الفعلية',
            self::GROUP_BACKUP => 'النسخ الاحتياطي',
            default => $this->action_group ?? '-',
        };
    }

    // Helper to get action type color
    public function getActionTypeColorAttribute(): string
    {
        return match ($this->action_type) {
            self::ACTION_LOGIN => 'success',
            self::ACTION_LOGOUT => 'secondary',
            self::ACTION_VIEW => 'info',
            self::ACTION_CREATE => 'primary',
            self::ACTION_UPDATE => 'warning',
            self::ACTION_DELETE, self::ACTION_BULK_DELETE => 'danger',
            self::ACTION_PRINT => 'purple',
            self::ACTION_BULK_IMPORT => 'cyan',
            self::ACTION_EXPORT => 'teal',
            self::ACTION_DOWNLOAD => 'success',
            self::ACTION_SEARCH => 'dark',
            self::ACTION_BACKUP => 'danger',
            default => 'secondary',
        };
    }

    // Helper to get action type icon
    public function getActionTypeIconAttribute(): string
    {
        return match ($this->action_type) {
            self::ACTION_LOGIN => 'ti-login',
            self::ACTION_LOGOUT => 'ti-logout',
            self::ACTION_VIEW => 'ti-eye',
            self::ACTION_CREATE => 'ti-plus',
            self::ACTION_UPDATE => 'ti-edit',
            self::ACTION_DELETE, self::ACTION_BULK_DELETE => 'ti-trash',
            self::ACTION_PRINT => 'ti-file-text',
            self::ACTION_BULK_IMPORT => 'ti-upload',
            self::ACTION_EXPORT => 'ti-file-export',
            self::ACTION_DOWNLOAD => 'ti-download',
            self::ACTION_SEARCH => 'ti-search',
            self::ACTION_BACKUP => 'ti-database-export',
            default => 'ti-activity',
        };
    }

    // Get old values from properties
    public function getOldValuesAttribute(): ?array
    {
        return $this->properties['old'] ?? null;
    }

    // Get new values from properties
    public function getNewValuesAttribute(): ?array
    {
        return $this->properties['new'] ?? null;
    }

    // Get affected IDs from properties (for bulk operations)
    public function getAffectedIdsAttribute(): ?array
    {
        return $this->properties['affected_ids'] ?? null;
    }
}
