@props(['confirm', 'label' => null])

<x-ui.tooltip :text="$label ?? __('labels.tables.delete')">
    <button type="button" {{ $attributes }} wire:confirm="{{ $confirm }}">
        @if($slot->isEmpty())
            <x-heroicon-o-trash class="w-6 h-6 hover:text-red-500"/>
        @else
            {{ $slot }}
        @endif
    </button>
</x-ui.tooltip>
