<?php

namespace App\Livewire\Concerns;

use App\Models\Unit;

trait WithUnitOptions
{
    public function getUnitOptionsProperty(): array
    {
        return Unit::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }
}
