<?php

namespace App\Support\Planner\Resources;

use App\Enums\VehicleTypeEnum;
use App\Models\Vehicle;
use App\Support\Planner\PlannerResource;
use Illuminate\Support\Collection;

/**
 * Covers both tractors and trailers — they're both `Vehicle` rows differing only by
 * `type`, so a single parameterized provider avoids two near-identical classes.
 */
class VehiclePlannerResourceProvider implements PlannerResourceProviderInterface
{
    public function __construct(private readonly VehicleTypeEnum $type) {}

    public function resources(): Collection
    {
        return Vehicle::query()
            ->where('type', $this->type->value)
            ->where('is_active', true)
            ->orderBy('registration_number')
            ->get(['id', 'registration_number'])
            ->map(fn (Vehicle $vehicle) => new PlannerResource($vehicle->id, $vehicle->registration_number));
    }

    public function transportSetForeignKey(): string
    {
        return match ($this->type) {
            VehicleTypeEnum::TRACTOR => 'vehicle_id',
            VehicleTypeEnum::TRAILER => 'trailer_id',
        };
    }
}
