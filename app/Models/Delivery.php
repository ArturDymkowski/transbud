<?php

namespace App\Models;

use App\Enums\DeliveryStatusEnum;
use Database\Factories\DeliveryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Delivery extends Model implements HasMedia
{
    /** @use HasFactory<DeliveryFactory> */
    use HasFactory, InteractsWithMedia, SoftDeletes;

    public const MEDIA_DOCUMENTS = 'documents';

    protected $fillable = [
        'number',
        'contractor_id',
        'contractor_address_id',
        'loading_address',
        'status',
    ];

    protected $casts = [
        'status' => DeliveryStatusEnum::class,
    ];

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    public function contractorAddress(): BelongsTo
    {
        return $this->belongsTo(ContractorAddress::class);
    }

    public function transportSets(): HasMany
    {
        return $this->hasMany(DeliveryTransportSet::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function ($q) use ($search) {
            $search = trim($search);
            $q->where(function ($q) use ($search) {
                $q->orWhere('number', 'like', '%'.$search.'%')
                    ->orWhere('loading_address', 'like', '%'.$search.'%')
                    ->orWhereHas('contractor', fn ($q2) => $q2->where('name', 'like', '%'.$search.'%'));
            });
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_DOCUMENTS)
            ->useDisk('delivery_documents')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf']);
    }
}
