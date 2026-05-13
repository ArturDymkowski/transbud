<?php

namespace App\Livewire\Tables;

use App\Models\Driver;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class DriversTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $isActive = '';
    public array $selected = [];

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    #[Computed]
    public function drivers()
    {
        return Driver::search($this->search)->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.tables.drivers-table', [
            'drivers' => Driver::search($this->search)
                ->when(filled($this->isActive), function ($query) {
                    return $query->where('is_active', $this->isActive);
                })
                ->paginate($this->perPage)
        ]);
    }
}
