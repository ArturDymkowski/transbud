<?php

namespace App\Livewire\Tables;

use App\Livewire\Concerns\WithFilters;
use App\Livewire\Concerns\WithPerPage;
use App\Models\Vehicle;
use Livewire\Component;
use Livewire\WithPagination;

class VehicleDriversTable extends Component
{
    use WithFilters, WithPagination, WithPerPage;

    public Vehicle $vehicle;

    public string $search = '';

    public function mount(Vehicle $vehicle): void
    {
        $this->vehicle = $vehicle;
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $drivers = $this->vehicle->drivers()
            ->when(filled($this->search), fn ($q) => $q->where('drivers.name', 'like', '%'.$this->search.'%'))
            ->orderByPivot('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.tables.vehicle-drivers-table', [
            'drivers' => $drivers,
        ]);
    }

    public function getActiveFiltersProperty(): array
    {
        $filters = [];

        if (filled($this->search)) {
            $filters[] = [
                'label' => __('labels.tables.search').': "'.$this->search.'"',
                'property' => 'search',
            ];
        }

        return $filters;
    }

    public function removeAssignment(int $driverId): void
    {
        $this->vehicle->drivers()->detach($driverId);

        $this->dispatch('notify', message: __('vehicles.driver_assignment_removed'));
    }
}
