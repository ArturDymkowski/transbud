<?php

namespace App\Livewire\Concerns;

trait WithBulkSelection
{
    public array $selected = [];
    public array $idsOnPage = [];

    protected function deleteSelectedRecords(string $modelClass): void
    {
        if (empty($this->selected)) {
            return;
        }

        $count = count($this->selected);
        $modelClass::whereIn('id', $this->selected)->delete();
        $this->selected = [];

        $this->dispatch('notify', message: trans('labels.general.deleted_success_count', [
            'count' => $count,
        ]));
    }
}
