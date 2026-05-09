<div class="flex flex-wrap items-center gap-8">

    <div x-data="{ selected: '' }">
        @foreach($options as $value => $label)
            @php
                $optionValue = is_numeric($value) ? $label : $value;
                $optionLabel = $label;
            @endphp

            <div class="mb-2">
                <label for="{{ $name }}_{{ $optionValue }}"
                       class="flex cursor-pointer items-center text-sm font-medium text-gray-700 select-none dark:text-gray-400">
                    <div class="relative">
                        <input
                            type="radio"
                            id="{{ $name }}_{{ $optionValue }}"
                            name="{{ $name }}"
                            value="{{ $optionValue }}"
                            class="sr-only"
                            @change="selected = '{{ $optionValue }}'"
                        />
                        <div
                            :class="selected === '{{ $optionValue }}' ? 'border-brand-500 bg-brand-500' : 'bg-transparent border-gray-300 dark:border-gray-700'"
                            class="hover:border-brand-500 dark:hover:border-brand-500 mr-3 flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]"
                        >
                        <span
                            class="h-2 w-2 rounded-full"
                            :class="selected === '{{ $optionValue }}' ? 'bg-white' : 'bg-white dark:bg-[#171f2e]'"
                        ></span>
                        </div>
                    </div>
                    {{ $optionLabel }}
                </label>
            </div>
        @endforeach
    </div>
</div>
