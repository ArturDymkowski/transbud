<?php

namespace App\Models;

use App\Enums\DeliveryTransportSetStatusEnum;
use Database\Factories\DeliveryTransportSetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryTransportSet extends Model
{
    /** @use HasFactory<DeliveryTransportSetFactory> */
    use HasFactory, SoftDeletes;

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

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function trailer(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'trailer_id');
    }
}
