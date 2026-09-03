<?php

namespace App\Livewire\Tables;

use App\Enums\CountriesEnum;
use App\Enums\DeliveryStatusEnum;
use App\Livewire\Concerns\WithBulkSelection;
use App\Livewire\Concerns\WithFilters;
use App\Livewire\Concerns\WithPerPage;
use App\Livewire\Concerns\WithTableSorting;
use App\Models\DeliveryTransportSet;
use App\Models\Driver;
use App\Models\Vehicle;
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

    public ?Vehicle $vehicle = null;

    public bool $readonly = false;

    public string $search = '';
    public string $isActive = '';
    public string $drivingLicenseExpiryDateFrom = '';
    public string $drivingLicenseExpiryDateTo = '';
    public string $identityCardExpiryDateFrom = '';
    public string $identityCardExpiryDateTo = '';
    public ?int $country = null;
    public string $trashed = '';

    public bool $showAssignModal = false;

    public string $selectedDriverId = '';

    public function mount(?Vehicle $vehicle = null, bool $readonly = false): void
    {
        $this->vehicle = ($vehicle && $vehicle->exists) ? $vehicle : null;
        $this->readonly = $readonly;
    }

    protected function filterFields(): array
    {
        return ['search', 'isActive', 'country', 'driving_license_expiry_date', 'identity_card_expiry_date', 'trashed'];
    }

    public function render()
    {
        $drivers = Driver::search($this->search)
            ->when($this->vehicle, fn ($q) => $q->whereHas('vehicles', fn ($vq) => $vq->where('vehicles.id', $this->vehicle->id)))
            ->when(filled($this->isActive), fn ($q) => $q->where('is_active', $this->isActive))
            ->when(filled($this->country), fn ($q) => $q->where('country', $this->country))
            ->when(filled($this->drivingLicenseExpiryDateFrom), fn ($q) => $q->where('driving_license_expiry_date', '>=', $this->drivingLicenseExpiryDateFrom))
            ->when(filled($this->drivingLicenseExpiryDateTo), fn ($q) => $q->where('driving_license_expiry_date', '<=', $this->drivingLicenseExpiryDateTo))
            ->when(filled($this->identityCardExpiryDateFrom), fn ($q) => $q->where('identity_card_expiry_date', '>=', $this->identityCardExpiryDateFrom))
            ->when(filled($this->identityCardExpiryDateTo), fn ($q) => $q->where('identity_card_expiry_date', '<=', $this->identityCardExpiryDateTo))
            ->when($this->trashed === 'with', fn ($q) => $q->withTrashed())
            ->when($this->trashed === 'only', fn ($q) => $q->onlyTrashed())
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $this->idsOnPage = $drivers->pluck('id')->toArray();

        return view('livewire.tables.drivers-table', [
            'drivers' => $drivers,
        ]);
    }

    public function deleteSelected(): void
    {
        if ($this->readonly) {
            return;
        }

        if ($this->vehicle) {
            $this->authorize('vehicles.edit');

            if (empty($this->selected)) {
                return;
            }

            $this->vehicle->drivers()->detach($this->selected);
            $this->selected = [];

            $this->dispatch('notify', message: __('vehicles.driver_assignment_removed'));

            return;
        }

        $this->authorize('drivers.delete');

        $this->deleteSelectedRecords(Driver::class);
    }

    public function deleteDriver(int $id): void
    {
        if ($this->readonly) {
            return;
        }

        if ($this->vehicle) {
            $this->authorize('vehicles.edit');

            $this->vehicle->drivers()->detach($id);
            $this->dispatch('notify', message: __('vehicles.driver_assignment_removed'));

            return;
        }

        $this->authorize('drivers.delete');

        $hasActiveDelivery = DeliveryTransportSet::where('driver_id', $id)
            ->whereHas('delivery', fn ($q) => $q->whereIn('status', [DeliveryStatusEnum::ASSIGNED->value, DeliveryStatusEnum::IN_PROGRESS->value]))
            ->exists();

        if ($hasActiveDelivery) {
            $this->dispatch('notify', message: __('labels.general.delete_blocked_active_delivery'), type: 'error');

            return;
        }

        Driver::where('id', $id)->delete();
        $this->dispatch('notify', message: __('labels.general.deleted_success'));
    }

    public function openAssignModal(): void
    {
        if ($this->readonly) {
            return;
        }

        $this->showAssignModal = true;
    }

    public function assignDriver(): void
    {
        if (! $this->vehicle || $this->readonly) {
            return;
        }

        $this->authorize('vehicles.edit');

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

    public function getAssignableDriverOptionsProperty(): array
    {
        if (! $this->vehicle) {
            return [];
        }

        $assignedIds = $this->vehicle->drivers()->pluck('drivers.id');

        $options = Driver::query()
            ->whereNotIn('id', $assignedIds)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        return ['' => __('labels.general.not_selected')] + $options;
    }

    public function toggleActive(int $driverId): void
    {
        if ($this->readonly) {
            return;
        }

        $this->authorize('drivers.edit');

        $driver = Driver::findOrFail($driverId);
        $driver->is_active = ! $driver->is_active;
        $driver->save();

        $this->dispatch('notify', message: __('labels.general.updated_success'));
    }

    public function editDriver(int $id): void
    {
        $this->dispatch('edit-driver', id: $id);
    }

    public function restoreDriver(int $id): void
    {
        if ($this->readonly) {
            return;
        }

        $this->authorize('drivers.edit');

        Driver::where('id', $id)->restore();
        $this->dispatch('notify', message: __('labels.general.restored_success'));
    }

    public function forceDeleteDriver(int $id): void
    {
        if ($this->readonly) {
            return;
        }

        $this->authorize('drivers.delete');

        $driver = Driver::onlyTrashed()->findOrFail($id);
        $driver->forceDelete();
        $this->dispatch('notify', message: __('labels.general.force_deleted_success'));
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

    public function getTrashedOptionsProperty(): array
    {
        return [
            '' => __('labels.tables.without_trashed'),
            'with' => __('labels.tables.with_trashed'),
            'only' => __('labels.tables.only_trashed'),
        ];
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

        if (filled($this->trashed)) {
            $filters[] = [
                'label' => $this->trashedOptions[$this->trashed],
                'property' => 'trashed',
            ];
        }

        if (filled($this->drivingLicenseExpiryDateFrom)) {
            $filters[] = [
                'label' => __('drivers.driving_license_expiry_date') . ' ' . mb_strtolower(__('labels.general.from')) . ': ' . $this->drivingLicenseExpiryDateFrom,
                'property' => 'drivingLicenseExpiryDateFrom',
            ];
        }

        if (filled($this->drivingLicenseExpiryDateTo)) {
            $filters[] = [
                'label' => __('drivers.driving_license_expiry_date') . ' ' . mb_strtolower(__('labels.general.to')) . ': ' . $this->drivingLicenseExpiryDateTo,
                'property' => 'drivingLicenseExpiryDateTo',
            ];
        }

        if (filled($this->identityCardExpiryDateFrom)) {
            $filters[] = [
                'label' => __('drivers.identity_card_expiry_date') . ' ' . mb_strtolower(__('labels.general.from')) . ': ' . $this->identityCardExpiryDateFrom,
                'property' => 'identityCardExpiryDateFrom',
            ];
        }

        if (filled($this->identityCardExpiryDateTo)) {
            $filters[] = [
                'label' => __('drivers.identity_card_expiry_date') . ' ' . mb_strtolower(__('labels.general.to')) . ': ' . $this->identityCardExpiryDateTo,
                'property' => 'identityCardExpiryDateTo',
            ];
        }

        return $filters;
    }
}
