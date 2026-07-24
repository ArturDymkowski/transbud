@props([
    'name',
    'options' => [],
    'label' => null,
    'required' => false,
    'placeholder' => null,
])

@php
    $jsOptions = collect($options)
        ->map(fn ($optionLabel, $value) => ['value' => (string) $value, 'label' => (string) $optionLabel])
        ->values();
@endphp

<div
    wire:key="searchable-select-{{ $name }}-{{ md5($jsOptions->toJson()) }}"
    x-data="searchableSelect(@js($jsOptions), @entangle($attributes->wire('model')))"
    @click.outside="close()"
    @keydown.escape.window="close()"
    class="relative"
>
    @if($label)
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ $label }} @if($required) <x-form.input.required-star /> @endif
        </label>
    @endif

    <button
        type="button"
        id="{{ $name }}"
        @click="toggle()"
        :aria-expanded="open"
        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 flex h-11 w-full items-center justify-between rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-left text-sm focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900"
        :class="selectedOption ? 'text-gray-800 dark:text-white/90' : 'text-gray-400 dark:text-white/30'"
    >
        <span x-text="selectedOption ? selectedOption.label : @js($placeholder ?? __('labels.general.not_selected'))" class="truncate"></span>
        <svg class="shrink-0 stroke-current text-gray-700 dark:text-gray-400" width="20" height="20" viewBox="0 0 20 20" fill="none"
             xmlns="http://www.w3.org/2000/svg" :class="open && 'rotate-180'">
            <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-40 mt-1.5 w-full rounded-xl border border-gray-200 bg-white p-2 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark"
    >
        <input
            type="text"
            x-ref="search"
            x-model="search"
            placeholder="{{ __('labels.tables.search_placeholder') }}"
            autocomplete="off"
            class="dark:bg-dark-900 mb-2 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90 dark:placeholder:text-white/30"
        />

        <ul class="max-h-56 space-y-0.5 overflow-y-auto">
            <template x-for="option in filteredOptions" :key="option.value">
                <li>
                    <button
                        type="button"
                        @click="choose(option)"
                        class="w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
                        :class="String(option.value) === String(selected) && 'bg-brand-50 text-brand-600 dark:bg-brand-500/[0.12] dark:text-brand-400'"
                        x-text="option.label"
                    ></button>
                </li>
            </template>

            <li x-show="filteredOptions.length === 0" class="px-3 py-2 text-sm text-gray-400 dark:text-gray-500">
                {{ __('labels.tables.no_results') }}
            </li>
        </ul>
    </div>
</div>
