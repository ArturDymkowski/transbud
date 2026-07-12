<?php

// app/Livewire/Concerns/WithPerPage.php
namespace App\Livewire\Concerns;

trait WithPerPage
{
    public int $perPage = 10;

    public function getOptionsPerPageProperty(): array
    {
        return [10 => 10, 25 => 25, 50 => 50, 100 => 100];
    }

    public function updatedPerPage(): void
    {
        $this->selected = [];
        $this->resetPage();
    }
}
