<!-- Language Switcher -->
<div class="relative group">

    <!-- Current language -->
    <button
        class="flex items-center gap-2 rounded-full bg-white dark:bg-gray-900 px-4 py-2 border border-gray-200 dark:border-gray-800 transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-800"
    >
        <span class="text-sm font-semibold tracking-widest text-brand-500">
            {{ Str::upper(app()->getLocale()) }}
        </span>

        <!-- Arrow -->
        <svg
            class="w-4 h-4 text-gray-800 dark:text-white/90 transition-transform duration-200 group-hover:rotate-180"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
            />
        </svg>
    </button>

    <!-- Dropdown -->
    <div
        class="absolute right-0 mt-2 w-44 overflow-hidden rounded-2xl bg-white dark:bg-gray-900 shadow-2xl opacity-0 invisible translate-y-2 group-hover:visible group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-200"
    >
        @foreach(config('app.available_locales') as $locale)
            <a href="{{ route('language.switch', $locale) }}"
               class="flex items-center gap-3 px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <span class="font-semibold tracking-widest text-brand-500">
                    {{ Str::upper($locale) }}
                </span>

                <span class="text-gray-800 dark:text-white/90">
                    {{ __('labels.languages.' . $locale) }}
                </span>
            </a>
        @endforeach
    </div>
</div>
