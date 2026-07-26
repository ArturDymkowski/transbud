<?php

namespace App\Livewire\Forms;

use App\Livewire\Concerns\WithSavedRedirect;
use App\Models\User;
use Livewire\Component;

class UsersForm extends Component
{
    use WithSavedRedirect;

    public array $userData = [];
    public ?User $user = null;

    public function mount(?User $user = null)
    {
        if ($user && $user->exists) {
            $this->user = $user;
        } else {
            $this->user = new User();
        }

        $this->userData = $this->user->only(['name', 'email']);
        $this->userData['password'] = '';
        $this->userData['password_confirmation'] = '';
    }

    protected function rules(): array
    {
        $passwordRule = $this->user->exists ? 'nullable' : 'required';

        return [
            'userData.name' => 'required|string|max:255',
            'userData.email' => 'required|email|max:255|unique:users,email,' . ($this->user?->id ?? 'NULL'),
            'userData.password' => $passwordRule . '|string|min:8|confirmed',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'userData.name' => __('users.name'),
            'userData.email' => __('users.email'),
            'userData.password' => __('users.password'),
        ];
    }

    public function save()
    {
        $this->validate();

        $isUpdate = $this->user->exists;

        $attributes = collect($this->userData)->except(['password', 'password_confirmation'])->all();

        if (filled($this->userData['password'])) {
            $attributes['password'] = $this->userData['password'];
        }

        if ($isUpdate) {
            $this->user->update($attributes);
        } else {
            $this->user->fill($attributes);
            $this->user->save();
        }

        return $this->flashSavedAndRedirect($isUpdate, 'users.index');
    }

    public function render()
    {
        return view('livewire.forms.users-form');
    }
}
