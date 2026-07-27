<?php

namespace App\Livewire\Shows;

use App\Models\Unit;
use Livewire\Component;

class UnitsShow extends Component
{
    public Unit $unit;

    public array $unitData = [];

    public function mount(Unit $unit)
    {
        $this->authorize('units.view');

        $this->unit = $unit;

        $this->unitData = $this->unit->only(['name']);
    }

    public function render()
    {
        return view('livewire.shows.units-show');
    }
}
