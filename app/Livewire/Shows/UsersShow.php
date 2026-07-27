<?php

namespace App\Livewire\Shows;

use App\Models\User;
use Livewire\Component;

class UsersShow extends Component
{
    public User $user;

    public array $userData = [];

    public function mount(User $user)
    {
        $this->authorize('users.view');

        $this->user = $user;

        $this->userData = $this->user->only(['name', 'email']);
    }

    public function render()
    {
        return view('livewire.shows.users-show');
    }
}
