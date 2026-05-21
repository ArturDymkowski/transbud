<?php

namespace App\Livewire\Tables;

use App\Enums\CountriesEnum;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class DriversTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $isActive = '';
    public ?int $country = null;
    public array $selected = [];
    public array $optionsPerPage  = [
        10 => 10,
        25 => 25,
        50 => 50,
        100 => 100,
    ];
    public int $perPage = 10;
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
            ->when(filled($this->country), function ($query) {
                return $query->where('country', $this->country);
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

    public function updatedSearch()
    {
        $this->resetPage();
    }


    public function updatedIsActive()
    {
        $this->resetPage();
    }


    public function resetFilters()
    {
        $this->reset(['search', 'isActive']);
        $this->resetPage();
    }

    public function editDriver($id)
    {
        $this->dispatch('edit-driver', id: $id);
    }

    public function getCountryOptionsProperty()
    {
        $options = \App\Enums\CountriesEnum::getOptions();

        return ['' => 'All'] + $options;
    }
}
