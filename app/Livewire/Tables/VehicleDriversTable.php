<?php

namespace App\Livewire\Tables;

use App\Livewire\Concerns\WithFilters;
use App\Livewire\Concerns\WithPerPage;
use App\Models\Driver;
use App\Models\Vehicle;
use Livewire\Component;
use Livewire\WithPagination;

class VehicleDriversTable extends Component
{
    use WithFilters, WithPagination, WithPerPage;

    public Vehicle $vehicle;

    public string $search = '';

    public bool $showAssignModal = false;

    public string $selectedDriverId = '';

    public function mount(Vehicle $vehicle): void
    {
        $this->vehicle = $vehicle;
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function openAssignModal(): void
    {
        $this->showAssignModal = true;
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

    public function getAssignableDriverOptionsProperty(): array
    {
        $assignedIds = $this->vehicle->drivers()->pluck('drivers.id');

        $options = Driver::query()
            ->whereNotIn('id', $assignedIds)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        return ['' => __('labels.general.not_selected')] + $options;
    }

    public function assignDriver(): void
    {
        $this->validate([
            'selectedDriverId' => 'required|exists:drivers,id',
        ], [], [
            'selectedDriverId' => __('drivers.singular_model_label'),
        ]);

        $this->vehicle->drivers()->syncWithoutDetaching([$this->selectedDriverId]);

        $this->reset('selectedDriverId');
        $this->showAssignModal = false;

        $this->dispatch('notify', message: __('vehicles.driver_assigned'));
    }
}
