<?php

namespace App\Livewire\Forms;

use App\Enums\VehicleTypeEnum;
use App\Livewire\Concerns\WithSavedRedirect;
use App\Models\Vehicle;
use Illuminate\Validation\Rules\Enum;
use Livewire\Component;

class VehiclesForm extends Component
{
    use WithSavedRedirect;

    public array $vehicleData = [];
    public ?Vehicle $vehicle = null;

    public function mount(?Vehicle $vehicle = null)
    {
        if ($vehicle && $vehicle->exists) {
            $this->vehicle = $vehicle;
        } else {
            $this->vehicle = new Vehicle();
        }

        $this->vehicleData = $this->vehicle->only([
            'registration_number', 'vin', 'type',
            'technical_inspection_expiry_date', 'insurance_expiry_date', 'tachograph_inspection_expiry_date',
            'additional_notes',
        ]);
    }

    protected function rules(): array
    {
        return [
            'vehicleData.registration_number' => 'required|string|max:255|unique:vehicles,registration_number,' . ($this->vehicle?->id ?? 'NULL'),
            'vehicleData.vin' => 'required|string|max:255|unique:vehicles,vin,' . ($this->vehicle?->id ?? 'NULL'),
            'vehicleData.type' => ['required', new Enum(VehicleTypeEnum::class)],
            'vehicleData.technical_inspection_expiry_date' => 'nullable|date',
            'vehicleData.insurance_expiry_date' => 'nullable|date',
            'vehicleData.tachograph_inspection_expiry_date' => 'nullable|date',
            'vehicleData.additional_notes' => 'nullable|string',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'vehicleData.registration_number' => __('vehicles.registration_number'),
            'vehicleData.vin' => __('vehicles.vin'),
            'vehicleData.type' => __('vehicles.type.type'),
            'vehicleData.technical_inspection_expiry_date' => __('vehicles.technical_inspection_expiry_date'),
            'vehicleData.insurance_expiry_date' => __('vehicles.insurance_expiry_date'),
            'vehicleData.tachograph_inspection_expiry_date' => __('vehicles.tachograph_inspection_expiry_date'),
            'vehicleData.additional_notes' => __('vehicles.additional_notes'),
        ];
    }

    public function save()
    {
        $this->validate();

        $isUpdate = $this->vehicle->exists;

        if ($isUpdate) {
            $this->vehicle->update($this->vehicleData);
        } else {
            $this->vehicle->fill($this->vehicleData);
            $this->vehicle->save();
        }

        return $this->flashSavedAndRedirect($isUpdate, 'vehicles.index');
    }

    public function render()
    {
        return view('livewire.forms.vehicles-form');
    }
}
