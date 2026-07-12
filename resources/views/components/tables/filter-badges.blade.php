@props(['filters' => []])

@if(count($filters))
    <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-2 mb-4 bg-gray-50 border border-gray-200 rounded-lg dark:bg-gray-800/40 dark:border-gray-700">
        <div class="flex flex-wrap items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
            <span class="font-medium text-gray-500 dark:text-gray-400">{{ __('labels.tables.filters') }}</span>

            @foreach($filters as $filter)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-brand-700 bg-brand-50 rounded-md border border-brand-200 dark:bg-brand-900/30 dark:text-brand-400 dark:border-brand-800">
                    {{ $filter['label'] }}
                    <button type="button" wire:click="$set('{{ $filter['property'] }}', '')" class="hover:text-brand-900 dark:hover:text-brand-200">
                        <x-heroicon-m-x-mark class="w-3.5 h-3.5"/>
                    </button>
                </span>
            @endforeach
        </div>

        <button type="button" wire:click="resetFilters" class="text-xs font-semibold transition text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
            {{ __('labels.tables.clear_all') }}
        </button>
    </div>
@endif
