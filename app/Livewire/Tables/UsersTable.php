<?php

namespace App\Livewire\Tables;

use App\Livewire\Concerns\WithAdminProtection;
use App\Livewire\Concerns\WithBulkSelection;
use App\Livewire\Concerns\WithFilters;
use App\Livewire\Concerns\WithPerPage;
use App\Livewire\Concerns\WithTableSorting;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UsersTable extends Component
{
    use WithAdminProtection, WithBulkSelection, WithFilters, WithPagination, WithPerPage, WithTableSorting;

    public string $search = '';

    public string $isActive = '';

    public string $trashed = '';

    public function mount(): void
    {
        $this->authorize('users.view');
    }

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

        if (in_array(auth()->id(), $this->selected)) {
            $this->dispatch('notify', message: __('users.cannot_delete_self'), type: 'error');

            return;
        }

        if ($this->anySelectedRequiresSuperAdmin($this->selected)) {
            $this->dispatch('notify', message: __('users.admin_accounts_require_super_admin'), type: 'error');

            return;
        }

        $this->deleteSelectedRecords(User::class);
    }

    public function deleteUser(int $id): void
    {
        $this->authorize('users.delete');

        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            $this->dispatch('notify', message: __('users.cannot_delete_self'), type: 'error');

            return;
        }

        if ($this->requiresSuperAdminToManage($user)) {
            $this->dispatch('notify', message: __('users.admin_accounts_require_super_admin'), type: 'error');

            return;
        }

        $user->delete();
        $this->dispatch('notify', message: __('labels.general.deleted_success'));
    }

    public function restoreUser(int $id): void
    {
        $this->authorize('users.edit');

        $user = User::onlyTrashed()->findOrFail($id);

        if ($this->requiresSuperAdminToManage($user)) {
            $this->dispatch('notify', message: __('users.admin_accounts_require_super_admin'), type: 'error');

            return;
        }

        $user->restore();
        $this->dispatch('notify', message: __('labels.general.restored_success'));
    }

    public function forceDeleteUser(int $id): void
    {
        $this->authorize('users.delete');

        $user = User::onlyTrashed()->findOrFail($id);

        if ($user->id === auth()->id()) {
            $this->dispatch('notify', message: __('users.cannot_delete_self'), type: 'error');

            return;
        }

        if ($this->requiresSuperAdminToManage($user)) {
            $this->dispatch('notify', message: __('users.admin_accounts_require_super_admin'), type: 'error');

            return;
        }

        $user->forceDelete();
        $this->dispatch('notify', message: __('labels.general.force_deleted_success'));
    }

    public function toggleActive(int $userId): void
    {
        $this->authorize('users.edit');

        $user = User::findOrFail($userId);

        if ($user->id === auth()->id()) {
            $this->dispatch('notify', message: __('users.cannot_change_own_status'), type: 'error');

            return;
        }

        if ($this->requiresSuperAdminToManage($user)) {
            $this->dispatch('notify', message: __('users.admin_accounts_require_super_admin'), type: 'error');

            return;
        }

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
                'label' => __('labels.tables.search').': "'.$this->search.'"',
                'property' => 'search',
            ];
        }

        if (filled($this->isActive)) {
            $filters[] = [
                'label' => __('labels.tables.active').': '.($this->isActive === '1'
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
