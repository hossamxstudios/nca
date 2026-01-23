<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stand extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lane_id',
        'name',
        'description',
    ];

    public function lane(): BelongsTo
    {
        return $this->belongsTo(Lane::class);
    }

    public function racks(): HasMany
    {
        return $this->hasMany(Rack::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function getRacksCountAttribute(): int
    {
        return $this->racks()->count();
    }
}
