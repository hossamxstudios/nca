<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ActivityLogger
{
    protected ?int $userId = null;
    protected ?string $actionType = null;
    protected ?string $actionGroup = null;
    protected ?Model $subject = null;
    protected ?string $subjectName = null;
    protected ?string $description = null;
    protected array $properties = [];
    protected ?string $batchId = null;
    protected ?int $batchCount = null;

    public static function make(): self
    {
        return new self();
    }

    public function by(?int $userId = null): self
    {
        $this->userId = $userId ?? Auth::id();
        return $this;
    }

    public function action(string $actionType, ?string $actionGroup = null): self
    {
        $this->actionType = $actionType;
        $this->actionGroup = $actionGroup;
        return $this;
    }

    public function on(?Model $subject, ?string $subjectName = null): self
    {
        $this->subject = $subject;
        $this->subjectName = $subjectName ?? $this->guessSubjectName($subject);
        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function withProperties(array $properties): self
    {
        $this->properties = array_merge($this->properties, $properties);
        return $this;
    }

    public function withOldValues(array $oldValues): self
    {
        $this->properties['old'] = $oldValues;
        return $this;
    }

    public function withNewValues(array $newValues): self
    {
        $this->properties['new'] = $newValues;
        return $this;
    }

    public function withChanges(array $oldValues, array $newValues): self
    {
        return $this->withOldValues($oldValues)->withNewValues($newValues);
    }

    public function withAffectedIds(array $ids): self
    {
        $this->properties['affected_ids'] = $ids;
        return $this;
    }

    public function batch(?string $batchId = null, ?int $count = null): self
    {
        $this->batchId = $batchId ?? (string) Str::uuid();
        $this->batchCount = $count;
        return $this;
    }

    public function log(): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $this->userId ?? Auth::id(),
            'action_type' => $this->actionType,
            'action_group' => $this->actionGroup,
            'subject_type' => $this->subject ? get_class($this->subject) : null,
            'subject_id' => $this->subject?->getKey(),
            'subject_name' => $this->subjectName,
            'description' => $this->description ?? $this->generateDescription(),
            'properties' => !empty($this->properties) ? $this->properties : null,
            'batch_id' => $this->batchId,
            'batch_count' => $this->batchCount,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    protected function guessSubjectName(?Model $subject): ?string
    {
        if (!$subject) {
            return null;
        }

        // Try common name attributes
        foreach (['name', 'title', 'file_name', 'email'] as $attr) {
            if (isset($subject->$attr)) {
                return $subject->$attr;
            }
        }

        return class_basename($subject) . ' #' . $subject->getKey();
    }

    protected function generateDescription(): string
    {
        $action = match ($this->actionType) {
            ActivityLog::ACTION_LOGIN => 'سجل دخول',
            ActivityLog::ACTION_LOGOUT => 'سجل خروج',
            ActivityLog::ACTION_VIEW => 'عرض',
            ActivityLog::ACTION_CREATE => 'أنشأ',
            ActivityLog::ACTION_UPDATE => 'عدّل',
            ActivityLog::ACTION_DELETE => 'حذف',
            ActivityLog::ACTION_PRINT => 'طبع',
            ActivityLog::ACTION_BULK_IMPORT => 'استورد',
            ActivityLog::ACTION_BULK_DELETE => 'حذف جماعي',
            ActivityLog::ACTION_EXPORT => 'صدّر',
            default => $this->actionType,
        };

        if ($this->subjectName) {
            return "{$action}: {$this->subjectName}";
        }

        return $action;
    }

    // Convenience static methods for common actions
    public static function login(): ActivityLog
    {
        return self::make()
            ->action(ActivityLog::ACTION_LOGIN, ActivityLog::GROUP_AUTH)
            ->description('تسجيل دخول إلى النظام')
            ->log();
    }

    public static function logout(): ActivityLog
    {
        return self::make()
            ->action(ActivityLog::ACTION_LOGOUT, ActivityLog::GROUP_AUTH)
            ->description('تسجيل خروج من النظام')
            ->log();
    }

    public static function viewed(Model $model, ?string $group = null): ActivityLog
    {
        return self::make()
            ->action(ActivityLog::ACTION_VIEW, $group)
            ->on($model)
            ->log();
    }

    public static function created(Model $model, ?string $group = null, ?array $attributes = null): ActivityLog
    {
        $logger = self::make()
            ->action(ActivityLog::ACTION_CREATE, $group)
            ->on($model);

        if ($attributes) {
            $logger->withNewValues($attributes);
        }

        return $logger->log();
    }

    public static function updated(Model $model, ?string $group = null, ?array $oldValues = null, ?array $newValues = null): ActivityLog
    {
        $logger = self::make()
            ->action(ActivityLog::ACTION_UPDATE, $group)
            ->on($model);

        if ($oldValues && $newValues) {
            $logger->withChanges($oldValues, $newValues);
        }

        return $logger->log();
    }

    public static function deleted(Model $model, ?string $group = null): ActivityLog
    {
        return self::make()
            ->action(ActivityLog::ACTION_DELETE, $group)
            ->on($model)
            ->log();
    }

    public static function printed(string $description, ?array $affectedIds = null, ?string $group = null): ActivityLog
    {
        $logger = self::make()
            ->action(ActivityLog::ACTION_PRINT, $group)
            ->description($description);

        if ($affectedIds) {
            $logger->withAffectedIds($affectedIds);
        }

        return $logger->log();
    }

    public static function bulkImported(string $description, array $affectedIds, ?string $group = null, ?string $batchId = null): ActivityLog
    {
        return self::make()
            ->action(ActivityLog::ACTION_BULK_IMPORT, $group)
            ->description($description)
            ->withAffectedIds($affectedIds)
            ->batch($batchId, count($affectedIds))
            ->log();
    }

    public static function bulkDeleted(string $description, array $affectedIds, ?string $group = null): ActivityLog
    {
        return self::make()
            ->action(ActivityLog::ACTION_BULK_DELETE, $group)
            ->description($description)
            ->withAffectedIds($affectedIds)
            ->batch(null, count($affectedIds))
            ->log();
    }
}
