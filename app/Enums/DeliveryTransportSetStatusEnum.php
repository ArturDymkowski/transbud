<?php

namespace App\Enums;

enum DeliveryTransportSetStatusEnum: int
{
    case DRAFT = 0;
    case ASSIGNED = 1;
    case LOADING = 2;
    case UNLOADING = 3;
    case IN_TRANSIT = 4;
    case COMPLETED = 5;
    case CANCELLED = 6;

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => __('deliveries.transport_set_status.draft'),
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
