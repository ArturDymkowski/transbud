<?php

namespace App\Helpers;

use App\Enums\CurrencyEnum;

/**
 * All money amounts in the app are stored as integers in grosze (1 PLN = 100 grosze)
 * to avoid float rounding issues. This helper is the single place that turns those
 * integers into a human-readable amount for display.
 */
class MoneyHelper
{
    public static function format(int $amountGrosze, CurrencyEnum $currency): string
    {
        return number_format($amountGrosze / 100, 2, ',', ' ').' '.$currency->label();
    }
}
