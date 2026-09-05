<?php

namespace App\Livewire\Concerns;

use App\Enums\DeliveryTransportSetStatusEnum;
use App\Models\DeliveryTransportSet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Shared query + presentation logic for rendering DeliveryTransportSet records
 * as calendar/planner events. Used by both the FullCalendar-based calendar and
 * the Planner, so the two views always show identical events, colors and titles.
 */
trait WithTransportSetEvents
{
    /**
     * @return Builder<DeliveryTransportSet>
     */
    private function transportSetEventsBetween(Carbon|string $start, Carbon|string $end): Builder
    {
        return DeliveryTransportSet::query()
            ->whereNotNull('loading_at')
            ->whereBetween('loading_at', [$start, $end])
            ->with(['delivery', 'driver', 'vehicle', 'trailer'])
            ->whereNotIn('status', [DeliveryTransportSetStatusEnum::CANCELLED, DeliveryTransportSetStatusEnum::DRAFT]);
    }

    private function transportSetEventTitle(DeliveryTransportSet $transportSet): string
    {
        $parts = array_filter([
            $transportSet->delivery->number,
            $transportSet->driver?->name,
        ]);

        return implode(' · ', $parts);
    }

    private function transportSetEventColor(DeliveryTransportSet $transportSet): string
    {
        return $transportSet->delivery->status->color();
    }
}
