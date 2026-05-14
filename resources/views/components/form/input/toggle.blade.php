<div x-data="{ switcherToggle: @js($isActive) }">
    <label for="{{ $name }}"
           class="flex cursor-pointer items-center gap-3 text-sm font-medium text-gray-700 select-none dark:text-gray-400">
        <div class="relative">
            <input {{ $attributes }} type="checkbox" id="{{ $name }}" class="sr-only" @change="switcherToggle = !switcherToggle" />
            <div class="block h-6 w-11 rounded-full"
                 :class="switcherToggle ? 'bg-brand-500 dark:bg-brand-500' : 'bg-gray-200 dark:bg-white/10'">
            </div>
            <div :class="switcherToggle ? 'translate-x-full' : 'translate-x-0'"
                 class="shadow-theme-sm absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white duration-300 ease-linear">
            </div>
        </div>
    </label>
</div>
