<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Import extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'filename',
        'original_filename',
        'type',
        'status',
        'total_rows',
        'processed_rows',
        'success_rows',
        'failed_rows',
        'errors',
        'summary',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'errors' => 'array',
        'summary' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_rows' => 'integer',
        'processed_rows' => 'integer',
        'success_rows' => 'integer',
        'failed_rows' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getProgressPercentageAttribute(): int
    {
        if ($this->total_rows === 0) {
            return 0;
        }
        return (int) round(($this->processed_rows / $this->total_rows) * 100);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'pending' => ['class' => 'bg-secondary', 'text' => 'قيد الانتظار', 'icon' => 'ti-clock'],
            'validating' => ['class' => 'bg-info', 'text' => 'جاري التحقق', 'icon' => 'ti-search'],
            'processing' => ['class' => 'bg-warning', 'text' => 'جاري الاستيراد', 'icon' => 'ti-loader'],
            'completed' => ['class' => 'bg-success', 'text' => 'مكتمل', 'icon' => 'ti-check'],
            'failed' => ['class' => 'bg-danger', 'text' => 'فشل', 'icon' => 'ti-x'],
            default => ['class' => 'bg-secondary', 'text' => 'غير معروف', 'icon' => 'ti-question-mark'],
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'full' => 'استيراد كامل',
            'clients' => 'عملاء فقط',
            'lands' => 'قطع فقط',
            'geographic' => 'مناطق جغرافية',
            default => 'غير معروف',
        };
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->whereIn('status', ['validating', 'processing']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
