@props(['event'])

<button type="button"
        wire:click="openTransportSet({{ $event->id }})"
        {{ $attributes->merge(['class' => 'absolute top-1 flex h-8 items-center overflow-hidden rounded-md px-2 text-xs font-medium text-white shadow-theme-xs transition hover:opacity-90']) }}
        style="left: {{ $event->offsetPercent }}%; width: {{ $event->widthPercent }}%; background-color: {{ $event->color }};"
        title="{{ $event->title }}">
    <span class="truncate">{{ $event->title }}</span>
</button>
