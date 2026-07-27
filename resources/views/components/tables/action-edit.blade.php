@props(['route'])

<x-ui.tooltip :text="__('labels.tables.edit')">
    <a href="{{ $route }}" wire:navigate>
        <x-heroicon-o-pencil-square class="w-6 h-6 hover:text-green-500"/>
    </a>
</x-ui.tooltip>
