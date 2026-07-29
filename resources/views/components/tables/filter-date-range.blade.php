@props(['prefix', 'label'])

<div class="flex flex-col">
    <x-form.input.date-picker name="{{ $prefix }}From"
                              label="{{ $label }}"
                              wire:model.live="{{ $prefix }}From"
                              placeholder="{{ __('labels.general.from') }}"/>

    <span class="text-center text-gray-700 dark:text-gray-400">-</span>

    <x-form.input.date-picker name="{{ $prefix }}To"
                              label=""
                              wire:model.live="{{ $prefix }}To"
                              placeholder="{{ __('labels.general.to') }}"/>
</div>
