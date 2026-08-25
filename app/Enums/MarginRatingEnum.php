<?php

namespace App\Enums;

/**
 * Buckets a delivery's margin percentage into a rating for the profitability
 * badge (list view and the "Opłacalność" tab). The buckets themselves are a
 * fixed, closed set; the percentage thresholds between them are configurable
 * via config('profitability.margin_thresholds').
 */
enum MarginRatingEnum: int
{
    case POOR = 0;
    case WARNING = 1;
    case GOOD = 2;

    /**
     * Null (revenue unknown/zero) is treated as WARNING - neither a confirmed
     * loss nor a confirmed good margin.
     */
    public static function fromPercent(?float $marginPercent): self
    {
        if ($marginPercent === null) {
            return self::WARNING;
        }

        $thresholds = config('profitability.margin_thresholds');

        return match (true) {
            $marginPercent < $thresholds['warning'] => self::POOR,
            $marginPercent < $thresholds['good'] => self::WARNING,
            default => self::GOOD,
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::POOR => '#f04438',
            self::WARNING => '#f79009',
            self::GOOD => '#12b76a',
        };
    }
}
