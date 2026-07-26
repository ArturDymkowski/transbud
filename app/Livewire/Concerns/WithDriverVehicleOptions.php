<?php

namespace App\Livewire\Concerns;

use App\Enums\VehicleTypeEnum;
use App\Models\Vehicle;
use Livewire\Attributes\Computed;

trait WithDriverVehicleOptions
{
    #[Computed]
    public function tractorOptions(): array
    {
        return $this->vehicleOptions(VehicleTypeEnum::TRACTOR);
    }

    #[Computed]
    public function trailerOptions(): array
    {
        return $this->vehicleOptions(VehicleTypeEnum::TRAILER);
    }

    private function vehicleOptions(VehicleTypeEnum $type): array
    {
        $options = ['' => __('labels.general.not_selected')];

        foreach (Vehicle::where('type', $type->value)->orderBy('registration_number')->get() as $vehicle) {
            $options[$vehicle->id] = $vehicle->registration_number;
        }

        return $options;
    }
}
