<div class="flex flex-wrap items-center gap-8">
    <div x-data="{ checkboxToggle: false }">
        <label for="{{ $name }}"
               class="flex cursor-pointer items-center text-sm text-gray-700 select-none dark:text-gray-400">
            <div class="relative">
                <input type="checkbox" id="{{ $name }}" name="{{ $name }}" class="sr-only" @if($required) required @endif
                       @change="checkboxToggle = !checkboxToggle" value="{{ $value }}" />
                <div :class="checkboxToggle ? 'border-brand-500 bg-brand-500' :
                        'bg-transparent border-gray-300 dark:border-gray-700'"
                     class="f hover:border-brand-500 dark:hover:border-brand-500 mr-3 flex h-5 w-5 items-center justify-center rounded-md border-[1.25px]">
                        <span :class="checkboxToggle ? '' : 'opacity-0'">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="white" stroke-width="1.94437"
                                      stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                </div>
            </div>
            {{ $slot }} @if($required) <x-form.input.required-star /> @endif
        </label>
    </div>
</div>
