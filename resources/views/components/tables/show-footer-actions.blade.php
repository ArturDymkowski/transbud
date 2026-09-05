@props(['indexRoute', 'editRoute'])

<div class="flex items-center justify-end w-full gap-3 mt-6">
    <x-ui.button class="w-full" size="sm" variant="outline" :href="$indexRoute" wire:navigate>{{ __('labels.general.close') }}</x-ui.button>
    @if($editRoute)
        <x-ui.button class="w-full" size="sm" variant="primary" :href="$editRoute" wire:navigate>{{ __('labels.tables.edit') }}</x-ui.button>
    @endif
</div>
