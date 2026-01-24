<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Backup extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'file_path',
        'file_size',
        'type',
        'status',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }

    public function getTypeArabicAttribute(): string
    {
        return match($this->type) {
            'database' => 'قاعدة البيانات',
            'files' => 'الملفات',
            'full' => 'كامل',
            default => $this->type,
        };
    }

    public function getStatusArabicAttribute(): string
    {
        return match($this->status) {
            'pending' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'failed' => 'فشل',
            default => $this->status,
        };
    }
}
