@props(['field', 'label', 'sortField', 'sortDirection'])

<th class="cursor-pointer px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400" wire:click="sortBy('{{ $field }}')">
    <div class="flex items-center gap-1">
        {{ $label }}

        {{-- Ikonki --}}
        @if($sortField === $field)
            @if($sortDirection === 'asc')
                <x-heroicon-o-chevron-up class="w-4 h-4" />
            @else
                <x-heroicon-o-chevron-down class="w-4 h-4" />
            @endif
        @else
            <x-heroicon-o-arrows-up-down class="w-4 h-4 text-gray-300" />
        @endif
    </div>
</th>
