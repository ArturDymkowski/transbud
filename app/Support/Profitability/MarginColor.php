<?php

namespace App\Support\Profitability;

use App\Helpers\ExpiryHelper;

/**
 * Maps a margin percentage to a badge color using config('profitability.margin_thresholds'),
 * shared by the profitability panel and the deliveries list so thresholds live in one place.
 */
final class MarginColor
{
    public static function forPercent(?float $marginPercent): string
    {
        if ($marginPercent === null) {
            return ExpiryHelper::COLORS[ExpiryHelper::YELLOW];
        }

        $thresholds = config('profitability.margin_thresholds');

        return match (true) {
            $marginPercent < $thresholds['warning'] => ExpiryHelper::COLORS[ExpiryHelper::RED],
            $marginPercent < $thresholds['good'] => ExpiryHelper::COLORS[ExpiryHelper::YELLOW],
            default => ExpiryHelper::COLORS[ExpiryHelper::GREEN],
        };
    }
}
