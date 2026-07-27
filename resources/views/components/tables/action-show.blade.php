@props(['route'])

<x-ui.tooltip :text="__('labels.tables.show')">
    <a href="{{ $route }}" wire:navigate>
        <x-heroicon-o-eye class="w-6 h-6 hover:text-brand-500"/>
    </a>
</x-ui.tooltip>
