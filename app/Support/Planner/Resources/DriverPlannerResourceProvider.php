<?php

namespace App\Support\Planner\Resources;

use App\Models\Driver;
use App\Support\Planner\PlannerResource;
use Illuminate\Support\Collection;

class DriverPlannerResourceProvider implements PlannerResourceProviderInterface
{
    public function resources(): Collection
    {
        return Driver::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Driver $driver) => new PlannerResource($driver->id, $driver->name));
    }
}
