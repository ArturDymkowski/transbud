@props(['hours', 'pxPerHour'])

<div class="flex sticky top-0 z-20 bg-white dark:bg-gray-900">
    <div class="sticky left-0 z-30 w-40 shrink-0 border-b border-r border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"></div>

    <div class="grid h-9 shrink-0 border-b border-gray-200 dark:border-gray-800"
         style="grid-template-columns: repeat({{ $hours->count() - 1 }}, {{ $pxPerHour }}px);">
        @foreach ($hours as $hour)
            @unless ($loop->last)
                <div class="flex items-center pl-1 text-xs font-medium text-gray-400 dark:text-gray-500">
                    {{ sprintf('%02d', $hour) }}
                </div>
            @endunless
        @endforeach
    </div>
</div>
