@props(['label' => null])

<td class="{{ $label ? 'flex items-start justify-between gap-4' : 'block' }} px-0 py-3 md:table-cell md:px-4 md:py-4 md:whitespace-nowrap">
    @if($label)
        <span class="shrink-0 text-xs font-medium text-gray-400 dark:text-gray-500 md:hidden">
            {{ $label }}
        </span>
    @endif
    <div {{ $attributes->merge(['class' => 'text-sm text-gray-500 dark:text-gray-400'.($label ? ' max-md:text-right' : '')]) }}>
        {{ $slot }}
    </div>
</td>
