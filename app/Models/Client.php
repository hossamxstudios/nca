<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Client extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'name',
        'national_id',
        'client_code',
        'files_code',
        'telephone',
        'mobile',
        'notes',
        'excel_row_number',
    ];

    protected $casts = [
        'files_code' => 'array',
    ];

    public function lands(): HasMany
    {
        return $this->hasMany(Land::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function mainFiles(): HasMany
    {
        return $this->hasMany(File::class)->whereNull('parent_id');
    }

    public function getLandsCountAttribute(): int
    {
        return $this->lands()->count();
    }

    public function getFilesCountAttribute(): int
    {
        return $this->mainFiles()->count();
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('national_id', 'like', "%{$search}%")
              ->orWhere('client_code', 'like', "%{$search}%")
              ->orWhere('mobile', 'like', "%{$search}%");
        });
    }

    public function scopeFilterByGovernorate($query, $governorateId)
    {
        return $query->whereHas('lands', function ($q) use ($governorateId) {
            $q->where('governorate_id', $governorateId);
        });
    }

    public static function generateClientCode(): string
    {
        $lastClient = static::withTrashed()->orderBy('id', 'desc')->first();
        $nextId = $lastClient ? $lastClient->id + 1 : 1;
        return 'NCA-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }
}
