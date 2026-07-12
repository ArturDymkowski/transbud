<?php

namespace App\Livewire\Tables;

use App\Enums\CountriesEnum;
use App\Livewire\Concerns\WithBulkSelection;
use App\Livewire\Concerns\WithFilters;
use App\Livewire\Concerns\WithPerPage;
use App\Livewire\Concerns\WithTableSorting;
use App\Models\Driver;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class DriversTable extends Component
{
    use WithPagination, WithTableSorting, WithPerPage, WithBulkSelection, WithFilters;

    public array $allowedSortFields = [
        'name', 'id', 'is_active',
        'driving_license_expiry_date', 'identity_card_expiry_date',
    ];

    public string $search = '';
    public string $isActive = '';
    public ?int $country = null;

    protected function filterFields(): array
    {
        return ['search', 'isActive', 'country'];
    }

    public function render()
    {
        Log::info("isActive: " . $this->isActive);
        $drivers = Driver::search($this->search)
            ->when(filled($this->isActive), fn ($q) => $q->where('is_active', $this->isActive))
            ->when(filled($this->country), fn ($q) => $q->where('country', $this->country))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $this->idsOnPage = $drivers->pluck('id')->toArray();

        return view('livewire.tables.drivers-table', [
            'drivers' => $drivers,
        ]);
    }

    public function deleteSelected(): void
    {
        $this->deleteSelectedRecords(Driver::class, 'kierowców');
    }

    public function deleteDriver(int $id): void
    {
        Driver::where('id', $id)->delete();
        $this->dispatch('notify', message: 'Pomyślnie usunięto');
    }

    public function toggleActive(int $driverId): void
    {
        $driver = Driver::findOrFail($driverId);
        $driver->is_active = ! $driver->is_active;
        $driver->save();

        $this->dispatch('notify', message: 'Rekord zaktualizowany');
    }

    public function editDriver(int $id): void
    {
        $this->dispatch('edit-driver', id: $id);
    }

    #[On('driver-updated')]
    public function refreshTable(): void
    {
        //
    }

    public function getCountryOptionsProperty(): array
    {
        return ['' => __('labels.tables.all')] + CountriesEnum::getOptions();
    }

    public function getActiveFiltersProperty(): array
    {
        $filters = [];

        if (filled($this->search)) {
            $filters[] = [
                'label' => __('labels.tables.search') . ': "' . $this->search . '"',
                'property' => 'search',
            ];
        }

        if (filled($this->isActive)) {
            $filters[] = [
                'label' => __('labels.tables.active') . ': ' . ($this->isActive === '1'
                        ? __('labels.tables.yes')
                        : __('labels.tables.no')),
                'property' => 'isActive',
            ];
        }

        if (filled($this->country)) {
            $filters[] = [
                'label' => __('labels.address.country') . ': ' . CountriesEnum::fromId($this->country)->label(),
                'property' => 'country',
            ];
        }

        return $filters;
    }
}
