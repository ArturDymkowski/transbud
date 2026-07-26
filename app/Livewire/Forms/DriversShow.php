<?php

namespace App\Livewire\Forms;

use App\Enums\VehicleTypeEnum;
use App\Livewire\Concerns\WithDriverDocuments;
use App\Livewire\Concerns\WithDriverVehicleOptions;
use App\Models\Driver;
use Livewire\Component;

class DriversShow extends Component
{
    use WithDriverDocuments, WithDriverVehicleOptions;

    public Driver $driver;

    public array $driverData = [];

    public function mount(Driver $driver)
    {
        $this->driver = $driver;

        $this->driverData = $this->driver->only([
            'name', 'phone', 'pesel', 'country', 'zipcode',
            'city', 'street', 'house_nr', 'apartment_nr', 'extra_info',
            'driving_license_number', 'driving_license_expiry_date',
            'identity_card_number', 'identity_card_expiry_date',
        ]);

        $this->driverData['tractor_id'] = $this->driver->vehicles()->where('type', VehicleTypeEnum::TRACTOR->value)->value('vehicles.id');
        $this->driverData['trailer_id'] = $this->driver->vehicles()->where('type', VehicleTypeEnum::TRAILER->value)->value('vehicles.id');
    }

    public function render()
    {
        return view('livewire.forms.drivers-show');
    }
}
