@props(['confirm', 'label' => null])

<x-ui.tooltip :text="$label ?? __('labels.tables.restore')">
    <button type="button" {{ $attributes }} wire:confirm="{{ $confirm }}">
        @if($slot->isEmpty())
            <x-heroicon-o-arrow-uturn-left class="w-6 h-6 hover:text-brand-500"/>
        @else
            {{ $slot }}
        @endif
    </button>
</x-ui.tooltip>
