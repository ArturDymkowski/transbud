<?php

namespace App\Support\Planner;

/**
 * A single row of the Planner (today: a driver). Kept independent of the
 * underlying Eloquent model so the row source can be swapped (drivers,
 * vehicles, trailers, ...) without touching the rendering code.
 */
final readonly class PlannerResource
{
    public function __construct(
        public int $id,
        public string $label,
    ) {}
}
