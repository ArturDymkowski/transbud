<?php

namespace App\Livewire\Tables;

use App\Enums\DeliveryStatusEnum;
use App\Livewire\Concerns\WithBulkSelection;
use App\Livewire\Concerns\WithFilters;
use App\Livewire\Concerns\WithPerPage;
use App\Livewire\Concerns\WithTableSorting;
use App\Models\DeliveryGood;
use App\Models\Good;
use Livewire\Component;
use Livewire\WithPagination;

class GoodsTable extends Component
{
    use WithPagination, WithTableSorting, WithPerPage, WithBulkSelection, WithFilters;

    public string $search = '';
    public string $isActive = '';
    public string $trashed = '';

    protected function filterFields(): array
    {
        return ['search', 'isActive', 'trashed'];
    }

    public function render()
    {
        $goods = Good::search($this->search)
            ->with('defaultUnit')
            ->when(filled($this->isActive), fn ($q) => $q->where('is_active', $this->isActive))
            ->when($this->trashed === 'with', fn ($q) => $q->withTrashed())
            ->when($this->trashed === 'only', fn ($q) => $q->onlyTrashed())
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $this->idsOnPage = $goods->pluck('id')->toArray();

        return view('livewire.tables.goods-table', [
            'goods' => $goods,
        ]);
    }

    public function deleteSelected(): void
    {
        $this->authorize('goods.delete');

        $this->deleteSelectedRecords(Good::class);
    }

    public function deleteGood(int $id): void
    {
        $this->authorize('goods.delete');

        $hasActiveDelivery = DeliveryGood::where('good_id', $id)
            ->whereHas('transportSet.delivery', fn ($q) => $q->whereIn('status', [DeliveryStatusEnum::ASSIGNED->value, DeliveryStatusEnum::IN_PROGRESS->value]))
            ->exists();

        if ($hasActiveDelivery) {
            $this->dispatch('notify', message: __('labels.general.delete_blocked_active_delivery'), type: 'error');

            return;
        }

        Good::where('id', $id)->delete();
        $this->dispatch('notify', message: __('labels.general.deleted_success'));
    }

    public function toggleActive(int $goodId): void
    {
        $this->authorize('goods.edit');

        $good = Good::findOrFail($goodId);
        $good->is_active = ! $good->is_active;
        $good->save();

        $this->dispatch('notify', message: __('labels.general.updated_success'));
    }

    public function restoreGood(int $id): void
    {
        $this->authorize('goods.edit');

        Good::where('id', $id)->restore();
        $this->dispatch('notify', message: __('labels.general.restored_success'));
    }

    public function forceDeleteGood(int $id): void
    {
        $this->authorize('goods.delete');

        $good = Good::onlyTrashed()->findOrFail($id);
        $good->forceDelete();
        $this->dispatch('notify', message: __('labels.general.force_deleted_success'));
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
