<?php

namespace App\Enums;

enum DeliveryStatusEnum: int
{
    case PLANNED = 0;
    case ASSIGNED = 1;
    case IN_PROGRESS = 2;
    case COMPLETED = 3;
    case CANCELLED = 4;

    public function label(): string
    {
        return match ($this) {
            self::PLANNED => __('deliveries.status.planned'),
            self::ASSIGNED => __('deliveries.status.assigned'),
            self::IN_PROGRESS => __('deliveries.status.in_progress'),
            self::COMPLETED => __('deliveries.status.completed'),
            self::CANCELLED => __('deliveries.status.cancelled'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PLANNED => 'gray',
            self::ASSIGNED => 'blue',
            self::IN_PROGRESS => 'yellow',
            self::COMPLETED => 'green',
            self::CANCELLED => 'red',
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
