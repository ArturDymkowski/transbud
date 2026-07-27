<?php

namespace App\Livewire\Forms;

use App\Livewire\Concerns\WithSavedRedirect;
use Livewire\Component;
use Spatie\Permission\Models\Permission;

class PermissionsForm extends Component
{
    use WithSavedRedirect;

    public array $permissionData = ['name' => ''];

    public ?Permission $permission = null;

    public function mount(Permission $permission): void
    {
        $this->permission = $permission;
        $this->permissionData['name'] = $permission->name;
    }

    protected function rules(): array
    {
        return [
            'permissionData.name' => 'required|string|max:255|unique:permissions,name,'.$this->permission->id,
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'permissionData.name' => __('permissions.name'),
        ];
    }

    public function save()
    {
        $this->authorize('permissions.edit');

        $this->validate();

        $this->permission->update(['name' => $this->permissionData['name']]);

        return $this->flashSavedAndRedirect(true, 'permissions.index');
    }

    public function render()
    {
        return view('livewire.forms.permissions-form');
    }
}
