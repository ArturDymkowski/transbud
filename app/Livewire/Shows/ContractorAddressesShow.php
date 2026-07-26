<?php

namespace App\Livewire\Shows;

use App\Livewire\Concerns\WithContractorOptions;
use App\Models\ContractorAddress;
use Livewire\Component;

class ContractorAddressesShow extends Component
{
    use WithContractorOptions;

    public ContractorAddress $contractorAddress;

    public array $addressData = [];

    public function mount(ContractorAddress $contractorAddress)
    {
        $this->contractorAddress = $contractorAddress;

        $this->addressData = $this->contractorAddress->only([
            'contractor_id', 'country', 'zipcode', 'city', 'street', 'house_nr', 'apartment_nr',
        ]);
    }

    public function render()
    {
        return view('livewire.shows.contractor-addresses-show');
    }
}
