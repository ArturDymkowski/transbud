<?php

namespace App\Models;

use App\Enums\DeliveryTransportSetStatusEnum;
use Database\Factories\DeliveryTransportSetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryTransportSet extends Model
{
    /** @use HasFactory<DeliveryTransportSetFactory> */
    use HasFactory;

    protected $fillable = [
        'delivery_id',
        'driver_id',
        'vehicle_id',
        'trailer_id',
        'loading_at',
        'unloading_at',
        'status',
    ];

    protected $casts = [
        'loading_at' => 'datetime',
        'unloading_at' => 'datetime',
        'status' => DeliveryTransportSetStatusEnum::class,
    ];

    /**
     * @return BelongsTo<Delivery, $this>
     */
    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    /**
     * @return BelongsTo<Driver, $this>
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * @return BelongsTo<Vehicle, $this>
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @return BelongsTo<Vehicle, $this>
     */
    public function trailer(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'trailer_id');
    }

    /**
     * @return HasMany<DeliveryGood, $this>
     */
    public function goods(): HasMany
    {
        return $this->hasMany(DeliveryGood::class);
    }

    /**
     * @return HasMany<DeliveryCost, $this>
     */
    public function costs(): HasMany
    {
        return $this->hasMany(DeliveryCost::class);
    }

    /**
     * @return HasMany<DeliveryTransportSetStatusHistory, $this>
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(DeliveryTransportSetStatusHistory::class)->latest('created_at');
    }
}
