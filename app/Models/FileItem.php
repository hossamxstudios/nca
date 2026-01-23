<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class FileItem extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'file_id',
        'item_id',
        'from_page',
        'to_page',
    ];

    protected $casts = [
        'from_page' => 'integer',
        'to_page' => 'integer',
    ];

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function getPagesRangeAttribute(): string
    {
        if ($this->to_page && $this->to_page !== $this->from_page) {
            return "ص {$this->from_page}-{$this->to_page}";
        }
        return "ص {$this->from_page}";
    }
}
