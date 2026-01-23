<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Land extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'governorate_id',
        'city_id',
        'district_id',
        'zone_id',
        'area_id',
        'room_id',
        'lane_id',
        'stand_id',
        'rack_id',
        'land_no',
        'unit_no',
        'address',
        'notes',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function lane(): BelongsTo
    {
        return $this->belongsTo(Lane::class);
    }

    public function stand(): BelongsTo
    {
        return $this->belongsTo(Stand::class);
    }

    public function rack(): BelongsTo
    {
        return $this->belongsTo(Rack::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function mainFiles(): HasMany
    {
        return $this->hasMany(File::class)->whereNull('parent_id');
    }

    public function getFilesCountAttribute(): int
    {
        return $this->mainFiles()->count();
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->governorate?->name,
            $this->city?->name,
            $this->district?->name,
            $this->zone?->name,
            $this->area?->name,
        ]);

        $address = implode(' - ', $parts);

        if ($this->land_no) {
            $address .= ' | قطعة: ' . $this->land_no;
        }
        if ($this->unit_no) {
            $address .= ' | وحدة: ' . $this->unit_no;
        }

        return $address;
    }

    public function getShortAddressAttribute(): string
    {
        return implode(' - ', array_filter([
            $this->governorate?->name,
            $this->city?->name,
            $this->land_no ? 'قطعة ' . $this->land_no : null,
        ]));
    }
}
