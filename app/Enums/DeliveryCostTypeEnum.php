<?php

namespace App\Enums;

enum DeliveryCostTypeEnum: int
{
    case FUEL = 0;
    case DRIVER = 1;
    case TOLL = 2;
    case FERRY = 3;
    case PARKING = 4;
    case SERVICE = 5;
    case OTHER = 6;

    public function label(): string
    {
        return match ($this) {
            self::FUEL => __('deliveries.cost_type.fuel'),
            self::DRIVER => __('deliveries.cost_type.driver'),
            self::TOLL => __('deliveries.cost_type.toll'),
            self::FERRY => __('deliveries.cost_type.ferry'),
            self::PARKING => __('deliveries.cost_type.parking'),
            self::SERVICE => __('deliveries.cost_type.service'),
            self::OTHER => __('deliveries.cost_type.other'),
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
