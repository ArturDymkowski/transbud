@props(['hours', 'pxPerHour'])

<div class="flex sticky top-0 z-20 bg-white dark:bg-gray-900">
    <div class="sticky left-0 z-30 w-40 shrink-0 border-b border-r border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>

    <div class="relative h-9 shrink-0 border-b border-gray-200 dark:border-gray-800"
         style="width: {{ ($hours->count() - 1) * $pxPerHour }}px;">
        @foreach ($hours as $hour)
            @unless ($loop->last)
                <div class="absolute top-0 flex h-full items-center text-xs font-medium text-gray-400 dark:text-gray-500"
                     style="left: {{ $hour * $pxPerHour }}px;">
                    {{ sprintf('%02d', $hour) }}
                </div>
            @endunless
        @endforeach
    </div>
</div>
