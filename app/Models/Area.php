<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'zone_id',
        'name',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function lands(): HasMany
    {
        return $this->hasMany(Land::class);
    }

    public function getLandsCountAttribute(): int
    {
        return $this->lands()->count();
    }
}
