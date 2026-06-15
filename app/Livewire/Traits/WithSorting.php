<?php

namespace App\Livewire\Traits;

trait WithSorting
{
    public $sortField = 'id';
    public $sortDirection = 'desc';

    public function sortBy($field)
    {
        if (property_exists($this, 'allowedSortFields') && !in_array($field, $this->allowedSortFields)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
    }
}
