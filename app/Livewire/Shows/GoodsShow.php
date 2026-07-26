<?php

namespace App\Livewire\Shows;

use App\Livewire\Concerns\WithUnitOptions;
use App\Models\Good;
use Livewire\Component;

class GoodsShow extends Component
{
    use WithUnitOptions;

    public Good $good;

    public array $goodData = [];

    public function mount(Good $good)
    {
        $this->good = $good;

        $this->goodData = $this->good->only(['name', 'description', 'default_unit_id']);
    }

    public function render()
    {
        return view('livewire.shows.goods-show');
    }
}
