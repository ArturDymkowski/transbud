<?php

namespace App\Livewire\Concerns;

trait WithSavedRedirect
{
    protected function flashSavedAndRedirect(bool $isUpdate, string $routeName)
    {
        session()->flash('success', $isUpdate ? trans('labels.general.updated_success') : trans('labels.general.saved_success'));

        return $this->redirect(route($routeName), navigate: true);
    }
}
