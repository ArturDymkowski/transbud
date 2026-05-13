<div class="flex flex-wrap items-center gap-8">
    <label for="{{ $name }}"
           class="flex cursor-pointer items-center text-sm text-gray-700 select-none dark:text-gray-400">
        <div class="relative w-5 h-5">

            <input type="checkbox"
                   id="{{ $name }}"
                   name="{{ $name }}"
                   class="sr-only peer"
                   value="{{ $value }}"
                   {{ $attributes }}
                   @if($required) required @endif
            />

            <div class="absolute inset-0 rounded-md border-[1.25px] transition-all
                        border-gray-300 dark:border-gray-700 bg-transparent
                        peer-checked:border-brand-500 peer-checked:bg-brand-500">
            </div>

            <div class="absolute inset-0 flex items-center justify-center opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11.6666 3.5L5.24992 9.91667L2.33325 7"
                          stroke="white"
                          stroke-width="2"
                          stroke-linecap="round"
                          stroke-linejoin="round" />
                </svg>
            </div>

        </div>

        @if($slot->isNotEmpty())
            <span class="ml-3">{{ $slot }}</span>
        @endif

        @if($required) <x-form.input.required-star /> @endif
    </label>
</div>
