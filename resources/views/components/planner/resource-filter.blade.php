@props(['label', 'options', 'allLabel' => null])

<div class="mb-3 flex items-center justify-end gap-2">
    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $label }}:</span>

    <div class="w-80">
        <x-form.input.multi-select
            name="planner-resource-filter"
            :options="$options"
            :placeholder="$allLabel"
            {{ $attributes }}
        />
    </div>
</div>
