<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class Governorate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function lands(): HasMany
    {
        return $this->hasMany(Land::class);
    }

    public function getCitiesCountAttribute(): int
    {
        return $this->cities()->count();
    }

    public function getLandsCountAttribute(): int
    {
        return $this->lands()->count();
    }
}
