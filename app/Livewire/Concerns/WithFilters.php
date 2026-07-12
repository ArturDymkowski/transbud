<?php


namespace App\Livewire\Concerns;

trait WithFilters
{
    protected function filterFields(): array
    {
        return ['search'];
    }

    public function updated(string $property): void
    {
        if (in_array($property, $this->filterFields(), true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset($this->filterFields());
        $this->resetPage();
    }
}
