<?php

namespace App\Livewire\Tables;

use App\Enums\VehicleTypeEnum;
use App\Livewire\Concerns\WithBulkSelection;
use App\Livewire\Concerns\WithFilters;
use App\Livewire\Concerns\WithPerPage;
use App\Livewire\Concerns\WithTableSorting;
use App\Models\Vehicle;
use Livewire\Component;
use Livewire\WithPagination;

class VehiclesTable extends Component
{
    use WithPagination, WithTableSorting, WithPerPage, WithBulkSelection, WithFilters;

    public string $search = '';
    public string $isActive = '';
    public string $trashed = '';
    public string $technicalInspectionExpiryDateFrom = '';
    public string $technicalInspectionExpiryDateTo = '';
    public string $insuranceExpiryDateFrom = '';
    public string $insuranceExpiryDateTo = '';
    public string $tachographInspectionExpiryDateFrom = '';
    public string $tachographInspectionExpiryDateTo = '';

    protected function filterFields(): array
    {
        return [
            'search', 'isActive', 'trashed',
            'technical_inspection_expiry_date', 'insurance_expiry_date', 'tachograph_inspection_expiry_date',
        ];
    }

    public function render()
    {
        $vehicles = Vehicle::search($this->search)
            ->when(filled($this->isActive), fn ($q) => $q->where('is_active', $this->isActive))
            ->when(filled($this->technicalInspectionExpiryDateFrom), fn ($q) => $q->where('technical_inspection_expiry_date', '>=', $this->technicalInspectionExpiryDateFrom))
            ->when(filled($this->technicalInspectionExpiryDateTo), fn ($q) => $q->where('technical_inspection_expiry_date', '<=', $this->technicalInspectionExpiryDateTo))
            ->when(filled($this->insuranceExpiryDateFrom), fn ($q) => $q->where('insurance_expiry_date', '>=', $this->insuranceExpiryDateFrom))
            ->when(filled($this->insuranceExpiryDateTo), fn ($q) => $q->where('insurance_expiry_date', '<=', $this->insuranceExpiryDateTo))
            ->when(filled($this->tachographInspectionExpiryDateFrom), fn ($q) => $q->where('tachograph_inspection_expiry_date', '>=', $this->tachographInspectionExpiryDateFrom))
            ->when(filled($this->tachographInspectionExpiryDateTo), fn ($q) => $q->where('tachograph_inspection_expiry_date', '<=', $this->tachographInspectionExpiryDateTo))
            ->when($this->trashed === 'with', fn ($q) => $q->withTrashed())
            ->when($this->trashed === 'only', fn ($q) => $q->onlyTrashed())
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $this->idsOnPage = $vehicles->pluck('id')->toArray();

        return view('livewire.tables.vehicles-table', [
            'vehicles' => $vehicles,
        ]);
    }

    public function deleteSelected(): void
    {
        $this->deleteSelectedRecords(Vehicle::class, __('vehicles.plural_model_label'));
    }

    public function getTypeOptionsProperty(): array
    {
        return collect(VehicleTypeEnum::cases())
            ->mapWithKeys(fn (VehicleTypeEnum $type) => [$type->value => $type->label()])
            ->all();
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

        if (filled($this->trashed)) {
            $filters[] = [
                'label' => $this->trashedOptions[$this->trashed],
                'property' => 'trashed',
            ];
        }

        return $filters;
    }
}
