<?php

namespace App\Livewire\Shows;

use App\Models\Delivery;
use Livewire\Component;

class DeliveriesShow extends Component
{
    public Delivery $delivery;

    public function mount(Delivery $delivery)
    {
        $this->authorize('deliveries.view');

        $this->delivery = $delivery->load([
            'contractor', 'contractorAddress',
            'transportSets.driver', 'transportSets.vehicle', 'transportSets.trailer',
            'transportSets.goods.good', 'transportSets.goods.unit',
            'transportSets.statusHistories.changedBy',
        ]);
    }

    public function render()
    {
        return view('livewire.shows.deliveries-show');
    }
}
