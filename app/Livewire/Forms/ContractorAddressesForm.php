<?php

namespace App\Livewire\Forms;

use App\Enums\CountriesEnum;
use App\Livewire\Concerns\WithSavedRedirect;
use App\Models\Contractor;
use App\Models\ContractorAddress;
use Illuminate\Validation\Rules\Enum;
use Livewire\Component;

class ContractorAddressesForm extends Component
{
    use WithSavedRedirect;

    public array $addressData = [];
    public ?ContractorAddress $contractorAddress = null;

    public function mount(?ContractorAddress $contractorAddress = null)
    {
        if ($contractorAddress && $contractorAddress->exists) {
            $this->contractorAddress = $contractorAddress;
        } else {
            $this->contractorAddress = new ContractorAddress();
        }

        $this->addressData = $this->contractorAddress->only([
            'contractor_id', 'country', 'zipcode', 'city', 'street', 'house_nr', 'apartment_nr',
        ]);
    }

    protected function rules(): array
    {
        return [
            'addressData.contractor_id' => 'required|exists:contractors,id',
            'addressData.country' => ['required', new Enum(CountriesEnum::class)],
            'addressData.zipcode' => 'required|string|max:20',
            'addressData.city' => 'required|string|max:100',
            'addressData.street' => 'required|string|max:100',
            'addressData.house_nr' => 'nullable|string|max:20',
            'addressData.apartment_nr' => 'nullable|string|max:20',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'addressData.contractor_id' => __('address_book.contractor'),
            'addressData.country' => __('labels.address.country'),
            'addressData.zipcode' => __('labels.address.zipcode'),
            'addressData.city' => __('labels.address.city'),
            'addressData.street' => __('labels.address.street'),
            'addressData.house_nr' => __('labels.address.house_nr'),
            'addressData.apartment_nr' => __('labels.address.apartment_nr'),
        ];
    }

    public function save()
    {
        $this->validate();

        $isUpdate = $this->contractorAddress->exists;

        if ($isUpdate) {
            $this->contractorAddress->update($this->addressData);
        } else {
            $this->contractorAddress->fill($this->addressData);
            $this->contractorAddress->save();
        }

        return $this->flashSavedAndRedirect($isUpdate, 'contractor-addresses.index');
    }

    public function getContractorOptionsProperty(): array
    {
        $options = ['' => __('labels.general.not_selected')];

        foreach (Contractor::orderBy('name')->get() as $contractor) {
            $options[$contractor->id] = $contractor->name;
        }

        return $options;
    }

    public function render()
    {
        return view('livewire.forms.contractor-addresses-form');
    }
}
