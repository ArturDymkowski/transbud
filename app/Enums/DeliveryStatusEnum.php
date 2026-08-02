<?php

namespace App\Enums;

enum DeliveryStatusEnum: int
{
    case PLANNED = 0; // brak przypisanych zestawów transportowych
    case ASSIGNED = 1; // wszystkie przypisane
    case IN_PROGRESS = 2; // chociaż jeden w trasie
    case COMPLETED = 3; // wszystkie dostarczone
    case CANCELLED = 4; // anulowane

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
            self::PLANNED => '#667085',
            self::ASSIGNED => '#0ba5ec',
            self::IN_PROGRESS => '#f79009',
            self::COMPLETED => '#12b76a',
            self::CANCELLED => '#f04438',
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
