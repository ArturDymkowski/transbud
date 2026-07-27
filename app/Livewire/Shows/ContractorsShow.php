<?php

namespace App\Livewire\Shows;

use App\Models\Contractor;
use Livewire\Component;

class ContractorsShow extends Component
{
    public Contractor $contractor;

    public array $contractorData = [];

    public function mount(Contractor $contractor)
    {
        $this->authorize('contractors.view');

        $this->contractor = $contractor;

        $this->contractorData = $this->contractor->only([
            'name', 'nip', 'regon', 'phone', 'email', 'notes',
        ]);
    }

    public function render()
    {
        return view('livewire.shows.contractors-show');
    }
}
