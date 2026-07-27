<?php

namespace App\Livewire\Forms;

use App\Livewire\Concerns\WithSavedRedirect;
use Illuminate\Support\Str;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesForm extends Component
{
    use WithSavedRedirect;

    public array $roleData = ['name' => ''];

    public array $selectedPermissions = [];

    public ?Role $role = null;

    public function mount(?Role $role = null): void
    {
        $this->role = ($role && $role->exists) ? $role : new Role;

        $this->roleData['name'] = $this->role->name ?? '';

        if ($this->role->exists) {
            $this->selectedPermissions = $this->role->permissions()->pluck('id')->map(fn ($id) => (string) $id)->all();
        }
    }

    protected function rules(): array
    {
        return [
            'roleData.name' => 'required|string|max:255|unique:roles,name,'.($this->role?->id ?? 'NULL'),
            'selectedPermissions' => 'array',
            'selectedPermissions.*' => 'exists:permissions,id',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'roleData.name' => __('roles.name'),
        ];
    }

    public function getAllPermissionsProperty()
    {
        return Permission::orderBy('name')->get();
    }

    public function getGroupedPermissionsProperty()
    {
        return $this->allPermissions->groupBy(fn (Permission $permission) => Str::before($permission->name, '.'));
    }

    public function save()
    {
        $this->authorize($this->role->exists ? 'roles.edit' : 'roles.create');

        $this->validate();

        $isUpdate = $this->role->exists;

        if ($isUpdate) {
            $this->role->update(['name' => $this->roleData['name']]);
        } else {
            $this->role = Role::create(['name' => $this->roleData['name']]);
        }

        $this->role->syncPermissions(Permission::whereIn('id', $this->selectedPermissions)->get());

        return $this->flashSavedAndRedirect($isUpdate, 'roles.index');
    }

    public function render()
    {
        return view('livewire.forms.roles-form');
    }
}
