<?php

namespace App\Enums;

enum CurrencyEnum: int
{
    case PLN = 0;
    case EUR = 1;
    case USD = 2;

    /**
     * ISO currency codes are not localized phrases, so this returns the case
     * name directly instead of going through __(), unlike the other enums.
     */
    public function label(): string
    {
        return $this->name;
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
