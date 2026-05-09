<div>
    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
        {{ Str::ucfirst($name) }} @if($required) <span class="text-error-500">*</span> @endif
    </label>
    <input type="{{ $type }}"
           placeholder="{{ $placeholder }}"
           value="{{ old($name, $value) }}"
           name="{{ $name }}"
           id="{{ $name }}"
           @if($required) required @endif

           @class([
                'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30',
                'border-gray-300 dark:border-gray-700' => !$errors->has($name),
                'border-red-300 dark:border-red-700' => $errors->has($name),
            ])/>
</div>
