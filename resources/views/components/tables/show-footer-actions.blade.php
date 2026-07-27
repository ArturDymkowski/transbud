@props(['indexRoute', 'editRoute'])

<div class="flex items-center justify-end w-full gap-3 mt-6">
    <x-ui.button class="w-full" size="sm" variant="outline">
        <a href="{{ $indexRoute }}" wire:navigate>{{ __('labels.general.close') }}</a>
    </x-ui.button>
    <x-ui.button class="w-full" size="sm" variant="primary">
        <a href="{{ $editRoute }}" wire:navigate>{{ __('labels.tables.edit') }}</a>
    </x-ui.button>
</div>
