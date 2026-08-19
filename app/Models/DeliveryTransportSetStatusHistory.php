<?php

namespace App\Models;

use App\Enums\DeliveryTransportSetStatusEnum;
use Database\Factories\DeliveryTransportSetStatusHistoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryTransportSetStatusHistory extends Model
{
    /** @use HasFactory<DeliveryTransportSetStatusHistoryFactory> */
    use HasFactory;

    public const UPDATED_AT = null;

    protected $table = 'delivery_transport_set_status_history';

    protected $fillable = [
        'delivery_transport_set_id',
        'status',
        'changed_by',
    ];

    protected $casts = [
        'status' => DeliveryTransportSetStatusEnum::class,
    ];

    public function transportSet(): BelongsTo
    {
        return $this->belongsTo(DeliveryTransportSet::class, 'delivery_transport_set_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
