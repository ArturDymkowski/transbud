<?php

namespace App\Helpers;

use Carbon\Carbon;

/**
 * Classifies expiry dates (driving license, ID card, vehicle inspections, ...)
 * into an urgency color used across the dashboard and table badges.
 */
class ExpiryHelper
{
    public const RED = 'red';

    public const YELLOW = 'yellow';

    public const GREEN = 'green';

    /**
     * Hex colors matching the palette already used by x-ui.status-badge / DeliveryStatusEnum.
     */
    public const COLORS = [
        self::RED => '#f04438',
        self::YELLOW => '#f79009',
        self::GREEN => '#12b76a',
    ];

    /**
     * Number of full days from today until the given date (negative if already past).
     */
    public static function daysRemaining(string|\DateTimeInterface|null $date): ?int
    {
        if (! $date) {
            return null;
        }

        return (int) Carbon::today()->diffInDays(Carbon::parse($date)->startOfDay(), false);
    }

    /**
     * Urgency bucket for a given expiry date, or null if it's not within the tracked window
     * (more than 30 days away).
     */
    public static function status(string|\DateTimeInterface|null $date): ?string
    {
        $days = self::daysRemaining($date);

        if ($days === null || $days > 30) {
            return null;
        }

        if ($days <= 7) {
            return self::RED;
        }

        if ($days <= 14) {
            return self::YELLOW;
        }

        return self::GREEN;
    }

    public static function color(string|\DateTimeInterface|null $date): ?string
    {
        $status = self::status($date);

        return $status ? self::COLORS[$status] : null;
    }
}
