<?php

namespace App\Enums;


enum VehicleTypeEnum: int
{
    case TRACTOR = 0;
    case TRAILER = 1;

    public function label(): string
    {
        return match ($this) {
            self::TRACTOR => __('vehicles.type.tractor'),
            self::TRAILER => __('vehicles.type.trailer'),
        };
    }

    public static function getOptions(): array
    {
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
