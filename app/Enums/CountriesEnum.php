<?php

namespace App\Enums;

enum CountriesEnum: int
{
    case BELARUS = 0;
    case MOLDOVA = 1;
    case POLAND = 2;
    case RUSSIA = 3;
    case UKRAINE = 4;
    case GERMANY = 5;
    case FRANCE = 6;
    case BELGIUM = 7;
    case AUSTRIA = 8;

    public function label(): string
    {
        return __('countries.' . $this->name);
    }

    public static function getOptions(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        asort($options);

        return $options;
    }

    public static function fromNullable(?int $value): ?self
    {
        if ($value === null) {
            return null;
        }

        return self::from($value);
    }


}
