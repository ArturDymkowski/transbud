<?php

namespace App\Livewire\Tables;

use App\Enums\DeliveryStatusEnum;
use App\Livewire\Concerns\WithBulkSelection;
use App\Livewire\Concerns\WithFilters;
use App\Livewire\Concerns\WithPerPage;
use App\Livewire\Concerns\WithTableSorting;
use App\Models\Delivery;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * @property-read array $trashedOptions
 */
class DeliveriesTable extends Component
{
    use WithBulkSelection, WithFilters, WithPagination, WithPerPage, WithTableSorting;

    public string $search = '';

    public string $status = '';

    public string $trashed = '';

    public function mount(): void
    {
        $this->authorize('deliveries.view');
    }

    protected function filterFields(): array
    {
        return ['search', 'status', 'trashed'];
    }

    public function render()
    {
        $query = Delivery::with(['contractor', 'contractorAddress'])
            ->withCount('transportSets')
            ->withSum('costs', 'amount')
            ->search($this->search)
            ->when(filled($this->status), fn ($q) => $q->where('status', $this->status))
            ->when($this->trashed === 'with', fn ($q) => $q->withTrashed())
            ->when($this->trashed === 'only', fn ($q) => $q->onlyTrashed());

        if ($this->sortField === 'contractor_name') {
            $query->join('contractors', 'contractors.id', '=', 'deliveries.contractor_id')
                ->orderBy('contractors.name', $this->sortDirection)
                ->select('deliveries.*');
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $deliveries = $query->paginate($this->perPage);

        $this->idsOnPage = $deliveries->pluck('id')->toArray();

        return view('livewire.tables.deliveries-table', [
            'deliveries' => $deliveries,
        ]);
    }

    public function deleteSelected(): void
    {
        $this->authorize('deliveries.delete');

        $hasActiveDelivery = Delivery::whereIn('id', $this->selected)
            ->whereIn('status', [DeliveryStatusEnum::ASSIGNED->value, DeliveryStatusEnum::IN_PROGRESS->value])
            ->exists();

        if ($hasActiveDelivery) {
            $this->dispatch('notify', message: __('labels.general.delete_blocked_active_delivery'), type: 'error');

            return;
        }

        $this->deleteSelectedRecords(Delivery::class);
    }

    public function deleteDelivery(int $id): void
    {
        $this->authorize('deliveries.delete');

        $delivery = Delivery::findOrFail($id);

        if (in_array($delivery->status, [DeliveryStatusEnum::ASSIGNED, DeliveryStatusEnum::IN_PROGRESS], true)) {
            $this->dispatch('notify', message: __('labels.general.delete_blocked_active_delivery'), type: 'error');

            return;
        }

        $delivery->delete();
        $this->dispatch('notify', message: __('labels.general.deleted_success'));
    }

    public function restoreDelivery(int $id): void
    {
        $this->authorize('deliveries.edit');

        Delivery::where('id', $id)->restore();
        $this->dispatch('notify', message: __('labels.general.restored_success'));
    }

    public function forceDeleteDelivery(int $id): void
    {
        $this->authorize('deliveries.delete');

        $delivery = Delivery::onlyTrashed()->findOrFail($id);
        $delivery->forceDelete();
        $this->dispatch('notify', message: __('labels.general.force_deleted_success'));
    }

    public function getStatusOptionsProperty(): array
    {
        return ['' => __('labels.tables.all')] + DeliveryStatusEnum::getOptions();
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
                'label' => __('labels.tables.search').': "'.$this->search.'"',
                'property' => 'search',
            ];
        }

        if (filled($this->status)) {
            $filters[] = [
                'label' => __('deliveries.status.status').': '.DeliveryStatusEnum::from((int) $this->status)->label(),
                'property' => 'status',
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
