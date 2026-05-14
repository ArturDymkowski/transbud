<?php

namespace App\Livewire\Tables;

use App\Models\Driver;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class DriversTable extends Component
{
    use WithPagination;

    public $search = '';
    public $isActive = '';
    public array $selected = [];
    public array $optionsPerPage  = [10, 25, 50, 100];
    public $perPage = 10;
    public array $idsOnPage = [];

    public function updatedPerPage()
    {
        $this->selected = [];
        $this->resetPage();
    }

    public function render()
    {
        $drivers = Driver::search($this->search)
            ->when(filled($this->isActive), function ($query) {
                return $query->where('is_active', $this->isActive);
            })
            ->paginate($this->perPage);

        $this->idsOnPage = $drivers->pluck('id')->toArray();

        return view('livewire.tables.drivers-table', [
            'drivers' => $drivers
        ]);
    }

    public function deleteSelected()
    {
        if (empty($this->selected)) {
            return;
        }
        $deletedRecords = count($this->selected);
        Driver::whereIn('id', $this->selected)->delete();
        $this->selected = [];

        $this->dispatch('notify', message: 'Pomyślnie usunięto ' . $deletedRecords . ' rekordów');
    }

    public function toggleActive($driverId)
    {
        $driver = Driver::findOrFail($driverId);
        $driver->is_active = !$driver->is_active;
        $driver->save();

        $this->dispatch('notify', message: 'Rekord zaktualizowany');
    }
}
