<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class File extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'parent_id',
        'client_id',
        'land_id',
        'room_id',
        'lane_id',
        'stand_id',
        'rack_id',
        'file_name',
        'barcode',
        'original_name',
        'page_number',
        'pages_count',
        'status',
        'error_message',
        'uploaded_by',
    ];

    protected $casts = [
        'pages_count' => 'integer',
        'page_number' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(File::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(File::class, 'parent_id');
    }

    public function pages(): HasMany
    {
        return $this->hasMany(File::class, 'parent_id')->orderBy('page_number');
    }

    public function subFiles(): HasMany
    {
        return $this->hasMany(File::class, 'parent_id')
            ->whereNull('page_number')
            ->whereNotNull('pages_count');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function land(): BelongsTo
    {
        return $this->belongsTo(Land::class);
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

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'file_items')
            ->withPivot(['from_page', 'to_page'])
            ->withTimestamps();
    }

    public function fileItems(): HasMany
    {
        return $this->hasMany(FileItem::class);
    }

    public function isMainFile(): bool
    {
        return is_null($this->parent_id);
    }

    public function isPage(): bool
    {
        return !is_null($this->parent_id);
    }

    public function getPhysicalLocationAttribute(): string
    {
        $parts = array_filter([
            $this->room?->building_name,
            $this->room?->name,
            $this->lane?->name,
            $this->stand?->name,
            $this->rack?->name,
        ]);

        return $parts ? implode(' → ', $parts) : 'غير محدد';
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'pending' => ['class' => 'bg-secondary', 'text' => 'قيد الانتظار'],
            'processing' => ['class' => 'bg-warning', 'text' => 'جاري المعالجة'],
            'completed' => ['class' => 'bg-success', 'text' => 'مكتمل'],
            'failed' => ['class' => 'bg-danger', 'text' => 'فشل'],
            default => ['class' => 'bg-secondary', 'text' => 'غير معروف'],
        };
    }

    public function scopeMainFiles($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopePageFiles($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Generate a unique barcode for the file
     * Format: NCA-YYYYMMDD-XXXXX (where XXXXX is a random 5-digit number)
     */
    public static function generateBarcode(): string
    {
        do {
            $barcode = 'NCA-' . date('Ymd') . '-' . str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::where('barcode', $barcode)->exists());

        return $barcode;
    }

    /**
     * Find file by barcode
     */
    public static function findByBarcode(string $barcode): ?self
    {
        return self::where('barcode', $barcode)->first();
    }

    /**
     * Scope to search by barcode
     */
    public function scopeByBarcode($query, string $barcode)
    {
        return $query->where('barcode', 'like', "%{$barcode}%");
    }
}
