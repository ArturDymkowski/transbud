<?php

namespace App\Livewire\Concerns;
trait WithTableFilters
{
    public string $search = '';
    public int $perPage = 10;

    // wspólne resetowanie paginacji przy zmianie filtra
    public function updated($property): void
    {
        if (in_array($property, $this->resetPageOn ?? ['search', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function getOptionsPerPageProperty(): array
    {
        return [10 => 10, 25 => 25, 50 => 50, 100 => 100];
    }
}
