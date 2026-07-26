<?php

namespace App\Livewire\Concerns;

use App\Models\Contractor;

trait WithContractorOptions
{
    public function getContractorOptionsProperty(): array
    {
        $options = ['' => __('labels.general.not_selected')];

        foreach (Contractor::orderBy('name')->get() as $contractor) {
            $options[$contractor->id] = $contractor->name;
        }

        return $options;
    }
}
