@props(['paginator'])

<div class="flex flex-col items-center justify-between gap-4 px-4 py-3 border-t border-gray-100 dark:border-gray-800 sm:flex-row">
    <div class="text-sm text-gray-600 dark:text-gray-400">
        @if($paginator->total() > 0)
            {{ __('labels.pagination.showing_from') }}
            <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $paginator->firstItem() }}</span>
            {{ __('labels.pagination.showing_to') }}
            <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $paginator->lastItem() }}</span>
            {{ __('labels.pagination.showing_of') }}
            <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $paginator->total() }}</span>
            {{ __('labels.pagination.showing_total') }}
        @else
            {{ __('labels.pagination.no_results') }}
        @endif
    </div>
    <div class="w-full sm:w-auto">{{ $paginator->links() }}</div>
</div>
