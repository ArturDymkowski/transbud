<?php

use App\Support\Planner\PlannerEvent;
use Illuminate\Support\Carbon;

test('offset and width are computed as percentages of the visible window', function () {
    $windowStart = Carbon::parse('2026-01-01 00:00');
    $windowEnd = Carbon::parse('2026-01-02 00:00');

    $event = PlannerEvent::forWindow(
        id: 1,
        resourceId: 1,
        title: 'DEL-1',
        color: '#000000',
        startsAt: Carbon::parse('2026-01-01 06:00'),
        endsAt: Carbon::parse('2026-01-01 12:00'),
        windowStart: $windowStart,
        windowEnd: $windowEnd,
    );

    expect($event->offsetPercent)->toEqualWithDelta(25.0, 0.01);
    expect($event->widthPercent)->toEqualWithDelta(25.0, 0.01);
});

test('events are clipped to the visible window boundaries', function () {
    $windowStart = Carbon::parse('2026-01-01 00:00');
    $windowEnd = Carbon::parse('2026-01-02 00:00');

    $event = PlannerEvent::forWindow(
        id: 1,
        resourceId: 1,
        title: 'DEL-1',
        color: '#000000',
        startsAt: Carbon::parse('2025-12-31 18:00'),
        endsAt: Carbon::parse('2026-01-02 06:00'),
        windowStart: $windowStart,
        windowEnd: $windowEnd,
    );

    expect($event->offsetPercent)->toEqualWithDelta(0.0, 0.01);
    expect($event->widthPercent)->toEqualWithDelta(100.0, 0.01);
});

test('very short events keep a minimum visible width', function () {
    $windowStart = Carbon::parse('2026-01-01 00:00');
    $windowEnd = Carbon::parse('2026-01-02 00:00');

    $event = PlannerEvent::forWindow(
        id: 1,
        resourceId: 1,
        title: 'DEL-1',
        color: '#000000',
        startsAt: Carbon::parse('2026-01-01 06:00'),
        endsAt: Carbon::parse('2026-01-01 06:00'),
        windowStart: $windowStart,
        windowEnd: $windowEnd,
    );

    expect($event->widthPercent)->toBeGreaterThanOrEqual(0.5);
});
