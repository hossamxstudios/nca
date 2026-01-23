<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'building_name',
        'description',
    ];

    public function lanes(): HasMany
    {
        return $this->hasMany(Lane::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function getLanesCountAttribute(): int
    {
        return $this->lanes()->count();
    }

    public function getFullNameAttribute(): string
    {
        return $this->building_name . ' - ' . $this->name;
    }
}
