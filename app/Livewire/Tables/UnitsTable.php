<?php

namespace App\Livewire\Tables;

use App\Livewire\Concerns\WithBulkSelection;
use App\Livewire\Concerns\WithFilters;
use App\Livewire\Concerns\WithPerPage;
use App\Livewire\Concerns\WithTableSorting;
use App\Models\Good;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;

class UnitsTable extends Component
{
    use WithPagination, WithTableSorting, WithPerPage, WithBulkSelection, WithFilters;

    public ?Good $good = null;

    public bool $readonly = false;

    public string $search = '';
    public string $isActive = '';
    public string $trashed = '';

    public bool $showAssignModal = false;

    public string $selectedUnitId = '';

    public function mount(?Good $good = null, bool $readonly = false): void
    {
        $this->good = ($good && $good->exists) ? $good : null;
        $this->readonly = $readonly;
    }

    protected function filterFields(): array
    {
        return ['search', 'isActive', 'trashed'];
    }

    public function render()
    {
        $units = Unit::search($this->search)
            ->when($this->good, fn ($q) => $q->whereHas('goods', fn ($gq) => $gq->where('goods.id', $this->good->id)))
            ->when(filled($this->isActive), fn ($q) => $q->where('is_active', $this->isActive))
            ->when($this->trashed === 'with', fn ($q) => $q->withTrashed())
            ->when($this->trashed === 'only', fn ($q) => $q->onlyTrashed())
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $this->idsOnPage = $units->pluck('id')->toArray();

        return view('livewire.tables.units-table', [
            'units' => $units,
        ]);
    }

    public function deleteSelected(): void
    {
        if ($this->readonly) {
            return;
        }

        if ($this->good) {
            $this->authorize('goods.edit');

            if (empty($this->selected)) {
                return;
            }

            $this->good->units()->detach($this->selected);
            $this->selected = [];

            $this->dispatch('notify', message: __('goods.unit_assignment_removed'));

            return;
        }

        $this->authorize('units.delete');

        $this->deleteSelectedRecords(Unit::class);
    }

    public function deleteUnit(int $id): void
    {
        if ($this->readonly) {
            return;
        }

        if ($this->good) {
            $this->authorize('goods.edit');

            $this->good->units()->detach($id);
            $this->dispatch('notify', message: __('goods.unit_assignment_removed'));

            return;
        }

        $this->authorize('units.delete');

        Unit::where('id', $id)->delete();
        $this->dispatch('notify', message: __('labels.general.deleted_success'));
    }

    public function openAssignModal(): void
    {
        if ($this->readonly) {
            return;
        }

        $this->showAssignModal = true;
    }

    public function assignUnit(): void
    {
        if (! $this->good || $this->readonly) {
            return;
        }

        $this->authorize('goods.edit');

        $this->validate([
            'selectedUnitId' => 'required|exists:units,id',
        ], [], [
            'selectedUnitId' => __('units.singular_model_label'),
        ]);

        $this->good->units()->syncWithoutDetaching([$this->selectedUnitId]);

        $this->reset('selectedUnitId');
        $this->showAssignModal = false;

        $this->dispatch('notify', message: __('goods.unit_assigned'));
    }

    public function getAssignableUnitOptionsProperty(): array
    {
        if (! $this->good) {
            return [];
        }

        $assignedIds = $this->good->units()->pluck('units.id');

        $options = Unit::query()
            ->whereNotIn('id', $assignedIds)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        return ['' => __('labels.general.not_selected')] + $options;
    }

    public function toggleActive(int $unitId): void
    {
        if ($this->readonly) {
            return;
        }

        $this->authorize('units.edit');

        $unit = Unit::findOrFail($unitId);
        $unit->is_active = ! $unit->is_active;
        $unit->save();

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
