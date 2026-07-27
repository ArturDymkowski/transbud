@props(['trashedOptions', 'name' => 'isActive'])

<x-form.input.select :label="__('labels.tables.trashed')" :options="$trashedOptions" name="trashed" wire:model.live="trashed"/>

<x-form.input.select :label="__('labels.tables.active')" :options="[
             '' => __('labels.tables.all'),
             0 => __('labels.tables.no'),
             1 => __('labels.tables.yes')
    ]" :name="$name" wire:model.live="{{ $name }}"/>
