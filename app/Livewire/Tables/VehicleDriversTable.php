<?php

namespace App\Livewire\Tables;

use App\Livewire\Concerns\WithPerPage;
use App\Models\Vehicle;
use Livewire\Component;
use Livewire\WithPagination;

class VehicleDriversTable extends Component
{
    use WithPagination, WithPerPage;

    public Vehicle $vehicle;

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
            ->orderByPivot('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.tables.vehicle-drivers-table', [
            'drivers' => $drivers,
        ]);
    }

    public function removeAssignment(int $driverId): void
    {
        $this->vehicle->drivers()->detach($driverId);

        $this->dispatch('notify', message: __('vehicles.driver_assignment_removed'));
    }
}
