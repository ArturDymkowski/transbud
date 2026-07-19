@props(['tabs', 'active' => null])

@php($active = $active ?? array_key_first($tabs))

<div x-data="{ activeTab: @js($active) }">
    <div class="mb-6 border-b border-gray-200 dark:border-gray-800">
        <nav class="-mb-px flex gap-6 overflow-x-auto" aria-label="Tabs">
            @foreach ($tabs as $key => $label)
                <button
                    type="button"
                    @click="activeTab = '{{ $key }}'"
                    :class="activeTab === '{{ $key }}'
                        ? 'border-brand-500 text-brand-500 dark:text-brand-400'
                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                    class="shrink-0 border-b-2 px-1 py-3 text-sm font-medium transition-colors"
                >
                    {{ $label }}
                </button>
            @endforeach
        </nav>
    </div>

    @foreach ($tabs as $key => $label)
        <div x-show="activeTab === '{{ $key }}'">
            {{ ${$key} ?? '' }}
        </div>
    @endforeach
</div>
