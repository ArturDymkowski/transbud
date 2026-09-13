<?php

namespace App\Livewire\Forms;

use App\Livewire\Concerns\WithAdminProtection;
use App\Livewire\Concerns\WithSavedRedirect;
use App\Models\Role;
use App\Models\User;
use Livewire\Component;

class UsersForm extends Component
{
    use WithAdminProtection, WithSavedRedirect;

    public array $userData = [];

    public ?User $user = null;

    public bool $isEditingSelf = false;

    public bool $protectedAdminFieldsReadOnly = false;

    public function mount(?User $user = null)
    {
        if ($user && $user->exists) {
            $this->user = $user;
        } else {
            $this->user = new User;
        }

        $this->isEditingSelf = $this->user->exists && $this->user->id === auth()->id();

        $this->protectedAdminFieldsReadOnly = $this->user->exists
            && ! $this->isEditingSelf
            && $this->requiresSuperAdminToManage($this->user);

        $this->userData = $this->user->only(['name', 'email']);

        if (! $this->user->exists) {
            $this->userData['password'] = '';
            $this->userData['password_confirmation'] = '';
        }

        $this->userData['role_id'] = $this->user->exists
            ? $this->user->roles()->value('roles.id')
            : null;
    }

    protected function rules(): array
    {
        $rules = [
            'userData.name' => 'required|string|max:255',
            'userData.email' => 'required|email|max:255|unique:users,email,'.($this->user->id ?? 'NULL'),
            'userData.role_id' => 'nullable|exists:roles,id',
        ];

        if (! $this->user->exists) {
            $rules['userData.password'] = 'required|string|min:8|confirmed';
        }

        return $rules;
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

        $isUpdate = $this->user->exists;

        $attributes = collect($this->userData)->except(['password', 'password_confirmation', 'role_id'])->all();

        if (! $isUpdate) {
            $attributes['password'] = $this->userData['password'];
        }

        if ($this->protectedAdminFieldsReadOnly) {
            $attributes['name'] = $this->user->name;
            $attributes['email'] = $this->user->email;
        }

        if ($isUpdate) {
            $this->user->update($attributes);
        } else {
            $this->user->fill($attributes);
            $this->user->save();
        }

        if (! $this->isEditingSelf && ! $this->protectedAdminFieldsReadOnly) {
            $this->user->syncRoles(array_filter([
                filled($this->userData['role_id'] ?? null) ? (int) $this->userData['role_id'] : null,
            ]));
        }

        return $this->flashSavedAndRedirect($isUpdate, 'users.index');
    }

    public function render()
    {
        return view('livewire.forms.users-form');
    }
}
