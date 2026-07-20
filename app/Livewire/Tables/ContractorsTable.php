<?php

namespace App\Livewire\Tables;

use App\Livewire\Concerns\WithBulkSelection;
use App\Livewire\Concerns\WithFilters;
use App\Livewire\Concerns\WithPerPage;
use App\Livewire\Concerns\WithTableSorting;
use App\Models\Contractor;
use Livewire\Component;
use Livewire\WithPagination;

class ContractorsTable extends Component
{
    use WithPagination, WithTableSorting, WithPerPage, WithBulkSelection, WithFilters;

    public array $allowedSortFields = ['name', 'id', 'active'];

    public string $search = '';
    public string $active = '';
    public string $trashed = '';

    protected function filterFields(): array
    {
        return ['search', 'active', 'trashed'];
    }

    public function render()
    {
        $contractors = Contractor::search($this->search)
            ->when(filled($this->active), fn ($q) => $q->where('active', $this->active))
            ->when($this->trashed === 'with', fn ($q) => $q->withTrashed())
            ->when($this->trashed === 'only', fn ($q) => $q->onlyTrashed())
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $this->idsOnPage = $contractors->pluck('id')->toArray();

        return view('livewire.tables.contractors-table', [
            'contractors' => $contractors,
        ]);
    }

    public function deleteSelected(): void
    {
        $this->deleteSelectedRecords(Contractor::class);
    }

    public function deleteContractor(int $id): void
    {
        Contractor::where('id', $id)->delete();
        $this->dispatch('notify', message: __('labels.general.deleted_success'));
    }

    public function toggleActive(int $contractorId): void
    {
        $contractor = Contractor::findOrFail($contractorId);
        $contractor->active = ! $contractor->active;
        $contractor->save();

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

        if (filled($this->active)) {
            $filters[] = [
                'label' => __('labels.tables.active') . ': ' . ($this->active === '1'
                        ? __('labels.tables.yes')
                        : __('labels.tables.no')),
                'property' => 'active',
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
