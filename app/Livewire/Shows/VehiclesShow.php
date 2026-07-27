<?php

namespace App\Livewire\Shows;

use App\Models\Vehicle;
use Livewire\Component;

class VehiclesShow extends Component
{
    public Vehicle $vehicle;

    public array $vehicleData = [];

    public function mount(Vehicle $vehicle)
    {
        $this->authorize('vehicles.view');

        $this->vehicle = $vehicle;

        $this->vehicleData = $this->vehicle->only([
            'registration_number', 'vin', 'type',
            'technical_inspection_expiry_date', 'insurance_expiry_date', 'tachograph_inspection_expiry_date',
            'additional_notes',
        ]);
    }

    public function render()
    {
        return view('livewire.shows.vehicles-show');
    }
}
