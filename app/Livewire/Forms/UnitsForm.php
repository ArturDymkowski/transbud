<?php

namespace App\Livewire\Forms;

use App\Livewire\Concerns\WithSavedRedirect;
use App\Models\Unit;
use Livewire\Component;

class UnitsForm extends Component
{
    use WithSavedRedirect;

    public array $unitData = [];
    public ?Unit $unit = null;

    public function mount(?Unit $unit = null)
    {
        if ($unit && $unit->exists) {
            $this->unit = $unit;
        } else {
            $this->unit = new Unit();
        }

        $this->unitData = $this->unit->only(['name']);
    }

    protected function rules(): array
    {
        return [
            'unitData.name' => 'required|string|max:255|unique:units,name,' . ($this->unit?->id ?? 'NULL'),
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'unitData.name' => __('units.name'),
        ];
    }

    public function save()
    {
        $this->validate();

        $isUpdate = $this->unit->exists;

        if ($isUpdate) {
            $this->unit->update($this->unitData);
        } else {
            $this->unit->fill($this->unitData);
            $this->unit->save();
        }

        return $this->flashSavedAndRedirect($isUpdate, 'units.index');
    }

    public function render()
    {
        return view('livewire.forms.units-form');
    }
}
