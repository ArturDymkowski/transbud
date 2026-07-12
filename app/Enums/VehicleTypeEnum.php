<?php

namespace App\Enums;


enum VehicleTypeEnum: int
{
    case TRACTOR = 0;
    case TRAILER = 1;

    public function labels(): ?string
    {
        return match ($this) {
            self::TRACTOR => __('vehicles.type.tractor'),
            self::TRAILER => __('vehicles.type.trailer'),
        };
    }

    public function label(): string
    {
        return self::labels()[$this->value];
    }
}
