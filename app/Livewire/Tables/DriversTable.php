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

    public string $search = '';
    public string $isActive = '';
    public array $selected = [];
    public array $optionsPerPage  = [
        10 => 10,
        25 => 25,
        50 => 50,
        100 => 100,
    ];
    public int $perPage = 10;
    public array $idsOnPage = [];

    public bool $showEditModal = false;
    public array $driverData = [];
    public ?\App\Models\Driver $editingDriver = null;

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

    // Formularz
    // Reguły walidacji
    protected function rules() {
        return [
            'driverData.name' => 'required|string|max:255',
            'driverData.phone' => 'required|string|max:30',
            'driverData.pesel' => 'required|string|size:11|unique:drivers,pesel,' . ($this->editingDriver?->id ?? 'NULL'),
            'driverData.country' => 'nullable|string|max:100',
            'driverData.region' => 'nullable|string|max:100',
            'driverData.zipcode' => 'nullable|string|max:20',
            'driverData.city' => 'nullable|string|max:100',
            'driverData.street' => 'nullable|string|max:100',
            'driverData.street_nr' => 'nullable|string|max:20',
            'driverData.home_nr' => 'nullable|string|max:20',
            'driverData.extra_info' => 'nullable|string',
            'driverData.driving_license_number' => 'required|string|unique:drivers,driving_license_number,' . ($this->editingDriver?->id ?? 'NULL'),
            'driverData.license_expiry_date' => 'required|date',
            'driverData.medical_exam_valid_until' => 'nullable|date',
            'driverData.is_active' => 'boolean',
        ];
    }

    // 1. Otwieranie modalu i ładowanie danych
    public function editDriver($id)
    {
        $this->editingDriver = \App\Models\Driver::findOrFail($id);

        // Przypisujemy dane do tablicy, z której korzysta formularz
        $this->driverData = $this->editingDriver->toArray();

        $this->showEditModal = true;
    }

    // 2. Zapisywanie zmian
    public function updateDriver()
    {
        $this->validate();

        $this->editingDriver->update($this->driverData);

        $this->showEditModal = false;
        $this->reset(['driverData', 'editingDriver']);

        // Wywołujemy nasz Toast, który zrobiliśmy wcześniej!
        $this->dispatch('notify', message: 'Dane kierowcy zostały pomyślnie zaktualizowane.');
    }
}
