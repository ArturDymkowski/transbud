@props(['deleteAction' => 'deleteSelected', 'confirmMessage'])

<div x-show="selected.length > 0"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 -translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 -translate-y-2"
     class="flex items-center justify-between px-4 py-3 mb-4 bg-brand-50 border border-brand-200 rounded-lg dark:bg-brand-900/20 dark:border-brand-800">
    <div class="flex items-center gap-2 text-sm font-medium text-brand-700 dark:text-brand-400">
        <span x-text="selected.length" class="flex items-center justify-center w-6 h-6 text-xs text-white rounded-full bg-brand-500"></span>
        <span>{{ __('labels.tables.selected_records') }}</span>
    </div>
    <div class="flex items-center gap-4">
        <button type="button" @click="selected = []" class="text-sm font-semibold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            {{ __('labels.tables.unselect_all') }}
        </button>
        <button type="button" wire:click="{{ $deleteAction }}" wire:confirm="{{ $confirmMessage }}"
                class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-white bg-red-600 rounded-md hover:bg-red-700 transition focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
            <x-heroicon-o-trash class="w-4 h-4"/>
        </button>
    </div>
</div>
