<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rack extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'stand_id',
        'name',
        'description',
    ];

    public function stand(): BelongsTo
    {
        return $this->belongsTo(Stand::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function getFilesCountAttribute(): int
    {
        return $this->files()->count();
    }

    public function getFullPathAttribute(): string
    {
        $stand = $this->stand;
        $lane = $stand?->lane;
        $room = $lane?->room;

        return implode(' → ', array_filter([
            $room?->building_name,
            $room?->name,
            $lane?->name,
            $stand?->name,
            $this->name,
        ]));
    }
}
