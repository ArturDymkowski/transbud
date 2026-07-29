<?php

namespace App\Enums;

enum DeliveryTransportSetStatusEnum: int
{
    case ASSIGNED = 0;
    case LOADING = 1;
    case UNLOADING = 2;
    case IN_TRANSIT = 3;
    case COMPLETED = 4;
    case CANCELLED = 5;

    public function label(): string
    {
        return match ($this) {
            self::ASSIGNED => __('deliveries.transport_set_status.assigned'),
            self::LOADING => __('deliveries.transport_set_status.loading'),
            self::UNLOADING => __('deliveries.transport_set_status.unloading'),
            self::IN_TRANSIT => __('deliveries.transport_set_status.in_transit'),
            self::COMPLETED => __('deliveries.transport_set_status.completed'),
            self::CANCELLED => __('deliveries.transport_set_status.cancelled'),
        };
    }

    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
