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
    public bool $showEditModal = false;
    public array $driverData = [];
    public ?\App\Models\Driver $editingDriver = null;

    protected function rules() {
        return [
            'driverData.name' => 'required|string|max:255',
            'driverData.phone' => 'required|string|max:30',
            'driverData.pesel' => 'required|string|size:11|unique:drivers,pesel,' . ($this->editingDriver?->id ?? 'NULL'),
            'driverData.country' => ['nullable', new Enum(CountriesEnum::class)],
            'driverData.region' => 'nullable|string|max:100',
            'driverData.zipcode' => 'nullable|string|max:20',
            'driverData.city' => 'nullable|string|max:100',
            'driverData.street' => 'nullable|string|max:100',
            'driverData.street_nr' => 'nullable|string|max:20',
            'driverData.home_nr' => 'nullable|string|max:20',
            'driverData.extra_info' => 'nullable|string',
            'driverData.driving_license_number' => 'required|string|unique:drivers,driving_license_number,' . ($this->editingDriver?->id ?? 'NULL'),
            'driverData.license_expiry_date' => 'required|date',
            'driverData.medical_exam_valid_until' => 'nullable|date',
            'driverData.is_active' => 'boolean',
        ];
    }

    #[On('edit-driver')]
    public function editDriver($id)
    {
        $this->editingDriver = \App\Models\Driver::findOrFail($id);

        $this->driverData = $this->editingDriver->toArray();

        $this->showEditModal = true;
    }

    public function updateDriver()
    {
        $this->validate();

        $this->editingDriver->update($this->driverData);

        $this->showEditModal = false;
        $this->reset(['driverData', 'editingDriver']);

        $this->dispatch('notify', message: 'Dane kierowcy zostały pomyślnie zaktualizowane.');
    }

    public function updatedDriverData($value, $key)
    {
        if ($value === '') {
            data_set($this->driverData, $key, null);
        }
    }
}
