<?php

namespace App\Livewire\Forms;

use App\Enums\CountriesEnum;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class DriversForm extends Component
{
    public array $driverData = [];
    public ?\App\Models\Driver $driver = null;

    public function mount(\App\Models\Driver $driver = null)
    {
        if ($driver && $driver->exists) {
            $this->driver = $driver;
            $this->driverData = $driver->toArray();
        }
    }

    protected function rules() {
        return [
            'driverData.name' => 'required|string|max:255',
            'driverData.phone' => 'required|string|max:30',
            'driverData.pesel' => 'required|string|size:11|unique:drivers,pesel,' . ($this->driver?->id ?? 'NULL'),
            'driverData.country' => ['nullable', new Enum(CountriesEnum::class)],
            'driverData.region' => 'nullable|string|max:100',
            'driverData.zipcode' => 'nullable|string|max:20',
            'driverData.city' => 'nullable|string|max:100',
            'driverData.street' => 'nullable|string|max:100',
            'driverData.street_nr' => 'nullable|string|max:20',
            'driverData.home_nr' => 'nullable|string|max:20',
            'driverData.extra_info' => 'nullable|string',
            'driverData.driving_license_number' => 'required|string|unique:drivers,driving_license_number,' . ($this->driver?->id ?? 'NULL'),
            'driverData.driving_license_expiry_date' => 'required|date',
            'driverData.identity_card_expiry_date' => 'nullable|date',
            'driverData.is_active' => 'boolean',
        ];
    }

    public function updateDriver()
    {
        $this->validate();

        // Wyciągamy dane z klucza driverData, ponieważ tak zdefiniowałeś reguły i bindowanie
        $this->driver->update($this->driverData['driverData'] ?? $this->driverData);

        // Zamiast czyszczenia komponentu i zamykania modala, przekierowujemy użytkownika z powrotem na listę
        session()->flash('notify', 'Dane kierowcy zostały pomyślnie zaktualizowane.');

        return $this->redirect(route('drivers.index'), navigate: true);
    }
}
