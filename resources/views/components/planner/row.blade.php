@props(['resource', 'events', 'hours', 'pxPerHour'])

<div {{ $attributes->merge(['class' => 'flex border-b border-gray-100 last:border-b-0 dark:border-gray-800']) }}>
    <div class="sticky left-0 z-10 flex w-40 shrink-0 items-center border-r border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
        <span class="truncate">{{ $resource->label }}</span>
    </div>

    <div class="relative grid h-10 shrink-0"
         style="grid-template-columns: repeat({{ $hours->count() - 1 }}, {{ $pxPerHour }}px);">
        @foreach ($hours as $hour)
            @unless ($loop->last)
                <div class="border-r border-gray-100 dark:border-gray-800"></div>
            @endunless
        @endforeach

        @foreach (($events ?? collect()) as $event)
            <x-planner.event :event="$event" wire:key="planner-event-{{ $event->id }}" />
        @endforeach
    </div>
</div>
