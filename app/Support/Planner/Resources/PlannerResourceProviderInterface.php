<?php

namespace App\Support\Planner\Resources;

use App\Support\Planner\PlannerResource;
use Illuminate\Support\Collection;

/**
 * Supplies the rows shown by the Planner. Implement this for each resource
 * type the Planner should be able to display (drivers, vehicles, trailers, ...).
 */
interface PlannerResourceProviderInterface
{
    /**
     * @return Collection<int, PlannerResource>
     */
    public function resources(): Collection;
}
