<?php

namespace App\Livewire\Tables;

use App\Models\Driver;
use Livewire\Component;
use Livewire\WithPagination;

class DriversTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 5;
    public $isActive = '';

    public function updatedPerPage()
    {
        $this->resetPage();
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
