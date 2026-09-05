<?php

namespace App\Support\Planner;

use Carbon\Carbon;

/**
 * A single block rendered on the Planner timeline. `offsetPercent`/`widthPercent`
 * are pre-computed relative to the visible hour window so the Blade layer only
 * has to set inline styles, keeping positioning logic testable outside Livewire.
 */
final readonly class PlannerEvent
{
    public function __construct(
        public int $id,
        public int $resourceId,
        public string $title,
        public string $color,
        public Carbon $startsAt,
        public Carbon $endsAt,
        public float $offsetPercent,
        public float $widthPercent,
    ) {}

    public static function forWindow(
        int $id,
        int $resourceId,
        string $title,
        string $color,
        Carbon $startsAt,
        Carbon $endsAt,
        Carbon $windowStart,
        Carbon $windowEnd,
    ): self {
        $windowSeconds = $windowEnd->getTimestamp() - $windowStart->getTimestamp();

        $clippedStart = max($startsAt->getTimestamp(), $windowStart->getTimestamp());
        $clippedEnd = min($endsAt->getTimestamp(), $windowEnd->getTimestamp());
        $clippedEnd = max($clippedEnd, $clippedStart);

        $offsetPercent = $windowSeconds > 0
            ? (($clippedStart - $windowStart->getTimestamp()) / $windowSeconds) * 100
            : 0.0;

        $widthPercent = $windowSeconds > 0
            ? (($clippedEnd - $clippedStart) / $windowSeconds) * 100
            : 0.0;

        // Keep very short/point-in-time events visible as a thin block.
        $widthPercent = max($widthPercent, 0.5);

        return new self($id, $resourceId, $title, $color, $startsAt, $endsAt, $offsetPercent, $widthPercent);
    }
}
