<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfileOverview extends Component
{
    public User $user;

    public array $profileData = [];

    public array $passwordData = [];

    public bool $showInfoModal = false;

    public bool $showPasswordModal = false;

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->profileData = $this->user->only(['name', 'email']);
    }

    public function getRoleNamesProperty(): string
    {
        return $this->user->getRoleNames()->implode(', ') ?: __('profile.no_role');
    }

    public function openInfoModal(): void
    {
        $this->profileData = $this->user->only(['name', 'email']);

        $this->resetValidation();
        $this->showInfoModal = true;
    }

    protected function infoRules(): array
    {
        return [
            'profileData.name' => 'required|string|max:255',
            'profileData.email' => 'required|email|max:255|unique:users,email,'.$this->user->id,
        ];
    }

    protected function infoValidationAttributes(): array
    {
        return [
            'profileData.name' => __('users.name'),
            'profileData.email' => __('users.email'),
        ];
    }

    public function saveInfo(): void
    {
        $validated = $this->validate($this->infoRules(), attributes: $this->infoValidationAttributes());

        $this->user->update($validated['profileData']);

        $this->showInfoModal = false;

        $this->dispatch('notify', message: __('labels.general.updated_success'));
    }

    public function openPasswordModal(): void
    {
        $this->passwordData = [
            'current_password' => '',
            'password' => '',
            'password_confirmation' => '',
        ];

        $this->resetValidation();
        $this->showPasswordModal = true;
    }

    protected function passwordRules(): array
    {
        return [
            'passwordData.current_password' => 'required|current_password',
            'passwordData.password' => 'required|string|min:8|confirmed',
        ];
    }

    protected function passwordValidationAttributes(): array
    {
        return [
            'passwordData.current_password' => __('profile.current_password'),
            'passwordData.password' => __('profile.new_password'),
        ];
    }

    public function savePassword(): void
    {
        $validated = $this->validate($this->passwordRules(), attributes: $this->passwordValidationAttributes());

        $this->user->update(['password' => $validated['passwordData']['password']]);

        $this->showPasswordModal = false;

        $this->dispatch('notify', message: __('labels.general.updated_success'));
    }

    public function render()
    {
        return view('livewire.profile.profile-overview');
    }
}
