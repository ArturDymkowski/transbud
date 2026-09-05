<?php

namespace App\Livewire\Tables;

use App\Livewire\Concerns\WithBulkSelection;
use App\Livewire\Concerns\WithFilters;
use App\Livewire\Concerns\WithPerPage;
use App\Livewire\Concerns\WithTableSorting;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

class PermissionsTable extends Component
{
    use WithBulkSelection, WithFilters, WithPagination, WithPerPage, WithTableSorting;

    public string $search = '';

    public function mount(): void
    {
        $this->authorize('permissions.view');
    }

    protected function filterFields(): array
    {
        return ['search'];
    }

    public function render()
    {
        $permissions = Permission::withCount('roles')
            ->when(filled($this->search), fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $this->idsOnPage = $permissions->pluck('id')->toArray();

        return view('livewire.tables.permissions-table', [
            'permissions' => $permissions,
        ]);
    }

    public function deleteSelected(): void
    {
        $this->authorize('permissions.delete');

        $this->deleteSelectedRecords(Permission::class);
    }

    public function deletePermission(int $id): void
    {
        $this->authorize('permissions.delete');

        Permission::where('id', $id)->delete();

        $this->dispatch('notify', message: __('labels.general.deleted_success'));
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

        return $filters;
    }
}
