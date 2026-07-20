<?php

namespace App\Livewire\Forms;

use App\Livewire\Concerns\WithSavedRedirect;
use App\Models\Contractor;
use Livewire\Component;

class ContractorsForm extends Component
{
    use WithSavedRedirect;

    public array $contractorData = [];
    public ?Contractor $contractor = null;

    public function mount(?Contractor $contractor = null)
    {
        if ($contractor && $contractor->exists) {
            $this->contractor = $contractor;
        } else {
            $this->contractor = new Contractor();
        }

        $this->contractorData = $this->contractor->only([
            'active', 'name', 'nip', 'regon', 'phone', 'email', 'notes',
        ]);

        if (! $this->contractor->exists) {
            $this->contractorData['active'] = true;
        }
    }

    protected function rules(): array
    {
        return [
            'contractorData.active' => 'boolean',
            'contractorData.name' => 'required|string|max:255',
            'contractorData.nip' => 'nullable|string|max:20',
            'contractorData.regon' => 'nullable|string|max:20',
            'contractorData.phone' => 'nullable|string|max:30',
            'contractorData.email' => 'nullable|email|max:255',
            'contractorData.notes' => 'nullable|string',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'contractorData.active' => __('labels.tables.active'),
            'contractorData.name' => __('contractors.name'),
            'contractorData.nip' => __('contractors.nip'),
            'contractorData.regon' => __('contractors.regon'),
            'contractorData.phone' => __('contractors.phone'),
            'contractorData.email' => __('contractors.email'),
            'contractorData.notes' => __('contractors.notes'),
        ];
    }

    public function save()
    {
        $this->validate();

        $isUpdate = $this->contractor->exists;

        if ($isUpdate) {
            $this->contractor->update($this->contractorData);
        } else {
            $this->contractor->fill($this->contractorData);
            $this->contractor->save();
        }

        return $this->flashSavedAndRedirect($isUpdate, 'contractors.index');
    }

    public function render()
    {
        return view('livewire.forms.contractors-form');
    }
}
