<?php

namespace App\Models;

use App\Enums\CurrencyEnum;
use App\Enums\DeliveryCostTypeEnum;
use Database\Factories\DeliveryCostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryCost extends Model
{
    /** @use HasFactory<DeliveryCostFactory> */
    use HasFactory;

    protected $fillable = [
        'delivery_id',
        'delivery_transport_set_id',
        'type',
        'amount',
        'currency',
        'description',
    ];

    protected $casts = [
        'type' => DeliveryCostTypeEnum::class,
        'amount' => 'integer',
        'currency' => CurrencyEnum::class,
    ];

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function transportSet(): BelongsTo
    {
        return $this->belongsTo(DeliveryTransportSet::class, 'delivery_transport_set_id');
    }
}
