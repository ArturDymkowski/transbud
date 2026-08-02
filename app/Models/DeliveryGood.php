<?php

namespace App\Models;

use Database\Factories\DeliveryGoodFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryGood extends Model
{
    /** @use HasFactory<DeliveryGoodFactory> */
    use HasFactory;

    protected $fillable = [
        'delivery_transport_set_id',
        'good_id',
        'unit_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    public function transportSet(): BelongsTo
    {
        return $this->belongsTo(DeliveryTransportSet::class, 'delivery_transport_set_id');
    }

    public function good(): BelongsTo
    {
        return $this->belongsTo(Good::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
