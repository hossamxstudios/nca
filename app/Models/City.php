<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'governorate_id',
        'name',
    ];

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    public function lands(): HasMany
    {
        return $this->hasMany(Land::class);
    }

    public function getDistrictsCountAttribute(): int
    {
        return $this->districts()->count();
    }
}
