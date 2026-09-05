<?php

namespace App\Livewire\Tables;

use App\Livewire\Concerns\WithBulkSelection;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class RolesTable extends Component
{
    use WithBulkSelection;

    public string $search = '';

    public function mount(): void
    {
        $this->authorize('roles.view');
    }

    public function render()
    {
        $roles = Role::withCount('permissions')
            ->when(filled($this->search), fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'))
            ->orderBy('id', 'desc')
            ->get();

        $this->idsOnPage = $roles->pluck('id')->toArray();

        return view('livewire.tables.roles-table', [
            'roles' => $roles,
        ]);
    }

    public function deleteRole(int $id): void
    {
        $this->authorize('roles.delete');

        $role = Role::findOrFail($id);

        if ($this->roleHasUsers($id)) {
            $this->dispatch('notify', message: __('roles.confirm_delete_role_in_use'), type: 'error');

            return;
        }

        $role->delete();
        $this->dispatch('notify', message: __('labels.general.deleted_success'));
    }

    public function deleteSelected(): void
    {
        $this->authorize('roles.delete');

        if (empty($this->selected)) {
            return;
        }

        $inUseIds = DB::table('model_has_roles')
            ->whereIn('role_id', $this->selected)
            ->where('model_type', User::class)
            ->pluck('role_id')
            ->unique()
            ->all();

        $deletableIds = array_diff($this->selected, $inUseIds);

        if (! empty($deletableIds)) {
            Role::whereIn('id', $deletableIds)->delete();
        }

        $this->selected = [];

        if (! empty($inUseIds)) {
            $this->dispatch('notify', message: __('roles.confirm_delete_role_in_use'), type: 'error');

            return;
        }

        $this->dispatch('notify', message: __('labels.general.deleted_success_count', ['count' => count($deletableIds)]));
    }

    private function roleHasUsers(int $roleId): bool
    {
        return DB::table('model_has_roles')
            ->where('role_id', $roleId)
            ->where('model_type', User::class)
            ->exists();
    }
}
