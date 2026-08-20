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

    /**
     * The DeliveryTransportSet column that links an event to one of these resources
     * (e.g. 'driver_id', 'vehicle_id', 'trailer_id'). Lets the Planner group events
     * correctly regardless of which resource type is currently shown.
     */
    public function transportSetForeignKey(): string;
}
