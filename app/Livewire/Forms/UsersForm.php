<?php

namespace App\Livewire\Forms;

use App\Livewire\Concerns\WithSavedRedirect;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class UsersForm extends Component
{
    use WithSavedRedirect;

    public array $userData = [];

    public ?User $user = null;

    public bool $isEditingSelf = false;

    public function mount(?User $user = null)
    {
        if ($user && $user->exists) {
            $this->user = $user;
        } else {
            $this->user = new User;
        }

        $this->isEditingSelf = $this->user->exists && $this->user->id === auth()->id();

        $this->userData = $this->user->only(['name', 'email']);
        $this->userData['password'] = '';
        $this->userData['password_confirmation'] = '';
        $this->userData['role_id'] = $this->user->exists
            ? $this->user->roles()->value('roles.id')
            : null;
    }

    protected function rules(): array
    {
        $passwordRule = $this->user->exists ? 'nullable' : 'required';

        return [
            'userData.name' => 'required|string|max:255',
            'userData.email' => 'required|email|max:255|unique:users,email,'.($this->user->id ?? 'NULL'),
            'userData.password' => $passwordRule.'|string|min:8|confirmed',
            'userData.role_id' => 'nullable|exists:roles,id',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'userData.name' => __('users.name'),
            'userData.email' => __('users.email'),
            'userData.password' => __('users.password'),
            'userData.role_id' => __('users.role'),
        ];
    }

    public function getRoleOptionsProperty(): array
    {
        return ['' => __('labels.general.not_selected')] + Role::orderBy('name')->pluck('name', 'id')->all();
    }

    public function save()
    {
        $this->authorize($this->user->exists ? 'users.edit' : 'users.create');

        $this->validate();

        if (! $this->isEditingSelf && $this->roleChangeGrantsOrRevokesAdmin() && ! auth()->user()?->is_super_admin) {
            throw ValidationException::withMessages([
                'userData.role_id' => trans('users.admin_role_requires_super_admin'),
            ]);
        }

        $isUpdate = $this->user->exists;

        $attributes = collect($this->userData)->except(['password', 'password_confirmation', 'role_id'])->all();

        if (filled($this->userData['password'])) {
            $attributes['password'] = $this->userData['password'];
        }

        if ($isUpdate) {
            $this->user->update($attributes);
        } else {
            $this->user->fill($attributes);
            $this->user->save();
        }

        if (! $this->isEditingSelf) {
            $this->user->syncRoles(array_filter([
                filled($this->userData['role_id'] ?? null) ? (int) $this->userData['role_id'] : null,
            ]));
        }

        return $this->flashSavedAndRedirect($isUpdate, 'users.index');
    }

    private function roleChangeGrantsOrRevokesAdmin(): bool
    {
        $newRoleId = filled($this->userData['role_id'] ?? null) ? (int) $this->userData['role_id'] : null;
        $adminRoleId = Role::where('name', 'Admin')->value('id');

        $isCurrentlyAdmin = $this->user->exists && $this->user->hasRole('Admin');
        $willBeAdmin = $adminRoleId !== null && $newRoleId === $adminRoleId;

        return $isCurrentlyAdmin !== $willBeAdmin;
    }

    public function render()
    {
        return view('livewire.forms.users-form');
    }
}
