<?php

namespace App\Livewire\Tables;

use App\Livewire\Concerns\WithBulkSelection;
use App\Livewire\Concerns\WithFilters;
use App\Livewire\Concerns\WithPerPage;
use App\Livewire\Concerns\WithTableSorting;
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
        $this->deleteSelectedRecords(Good::class);
    }

    public function deleteGood(int $id): void
    {
        Good::where('id', $id)->delete();
        $this->dispatch('notify', message: __('labels.general.deleted_success'));
    }

    public function toggleActive(int $goodId): void
    {
        $good = Good::findOrFail($goodId);
        $good->is_active = ! $good->is_active;
        $good->save();

        $this->dispatch('notify', message: __('labels.general.updated_success'));
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
