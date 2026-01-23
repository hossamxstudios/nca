<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lane extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_id',
        'name',
        'description',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function stands(): HasMany
    {
        return $this->hasMany(Stand::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function getStandsCountAttribute(): int
    {
        return $this->stands()->count();
    }
}
