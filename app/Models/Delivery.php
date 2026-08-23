<?php

namespace App\Models;

use App\Enums\CurrencyEnum;
use App\Enums\DeliveryStatusEnum;
use Database\Factories\DeliveryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Delivery extends Model implements HasMedia
{
    /** @use HasFactory<DeliveryFactory> */
    use HasFactory, InteractsWithMedia;

    public const MEDIA_DOCUMENTS = 'documents';

    protected $fillable = [
        'number',
        'contractor_id',
        'contractor_address_id',
        'loading_address',
        'status',
        'freight_amount',
        'currency',
    ];

    protected $casts = [
        'status' => DeliveryStatusEnum::class,
        'freight_amount' => 'integer',
        'currency' => CurrencyEnum::class,
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

    /**
     * All costs of this delivery, both those attached directly to it and those
     * attached to one of its transport sets (delivery_id is always set on both).
     */
    public function costs(): HasMany
    {
        return $this->hasMany(DeliveryCost::class);
    }

    /**
     * Costs that belong to the delivery as a whole, not to a specific transport set.
     */
    public function directCosts(): HasMany
    {
        return $this->costs()->whereNull('delivery_transport_set_id');
    }

    /**
     * Total cost of the delivery, in grosze. Uses the `costs_sum_amount` aggregate
     * from `withSum('costs', 'amount')` when available (e.g. in table listings) to
     * avoid an extra query, otherwise falls back to a live sum.
     */
    public function totalCostAmount(): int
    {
        return $this->costs_sum_amount ?? $this->costs()->sum('amount');
    }

    /**
     * Profit of the delivery, in grosze. Freight amount left empty is treated as 0.
     */
    public function profitAmount(): int
    {
        return ($this->freight_amount ?? 0) - $this->totalCostAmount();
    }

    /**
     * Margin as a percentage of revenue, or null when revenue is 0/unset
     * (division by zero is undefined, not "0%").
     */
    public function marginPercent(): ?float
    {
        $revenue = $this->freight_amount ?? 0;

        if ($revenue <= 0) {
            return null;
        }

        return round($this->profitAmount() / $revenue * 100, 2);
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
