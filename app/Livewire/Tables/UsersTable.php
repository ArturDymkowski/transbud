<?php

namespace App\Livewire\Tables;

use App\Livewire\Concerns\WithBulkSelection;
use App\Livewire\Concerns\WithFilters;
use App\Livewire\Concerns\WithPerPage;
use App\Livewire\Concerns\WithTableSorting;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UsersTable extends Component
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
        $users = User::search($this->search)
            ->with('roles')
            ->when(filled($this->isActive), fn ($q) => $q->where('is_active', $this->isActive))
            ->when($this->trashed === 'with', fn ($q) => $q->withTrashed())
            ->when($this->trashed === 'only', fn ($q) => $q->onlyTrashed())
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $this->idsOnPage = $users->pluck('id')->toArray();

        return view('livewire.tables.users-table', [
            'users' => $users,
        ]);
    }

    public function deleteSelected(): void
    {
        $this->authorize('users.delete');

        $this->deleteSelectedRecords(User::class);
    }

    public function deleteUser(int $id): void
    {
        $this->authorize('users.delete');

        User::where('id', $id)->delete();
        $this->dispatch('notify', message: __('labels.general.deleted_success'));
    }

    public function toggleActive(int $userId): void
    {
        $this->authorize('users.edit');

        $user = User::findOrFail($userId);
        $user->is_active = ! $user->is_active;
        $user->save();

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
