@props(['resource', 'events', 'hoursCount', 'pxPerHour'])

<div {{ $attributes->merge(['class' => 'flex border-b border-gray-100 last:border-b-0 dark:border-gray-800']) }}>
    <div class="sticky left-0 z-10 flex w-40 shrink-0 items-center border-r border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
        <span class="truncate">{{ $resource->label }}</span>
    </div>

    <div class="relative h-10 shrink-0"
         style="width: {{ ($hoursCount - 1) * $pxPerHour }}px; background-image: repeating-linear-gradient(to right, rgba(148,163,184,0.25) 0, rgba(148,163,184,0.25) 1px, transparent 1px, transparent {{ $pxPerHour }}px);">
        @foreach (($events ?? collect()) as $event)
            <x-planner.event :event="$event" wire:key="planner-event-{{ $event->id }}" />
        @endforeach
    </div>
</div>
