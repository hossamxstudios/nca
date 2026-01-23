<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'order',
    ];

    public function files(): BelongsToMany
    {
        return $this->belongsToMany(File::class, 'file_items')
            ->withPivot(['from_page', 'to_page'])
            ->withTimestamps();
    }

    public function fileItems(): HasMany
    {
        return $this->hasMany(FileItem::class);
    }

    public function getFilesCountAttribute(): int
    {
        return $this->files()->count();
    }
}
