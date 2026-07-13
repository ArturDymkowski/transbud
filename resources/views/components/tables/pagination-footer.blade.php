@props(['paginator'])

<div class="flex flex-col items-center justify-between gap-4 px-4 py-3 border-t border-gray-100 dark:border-gray-800 sm:flex-row">

    <div class="flex flex-col items-center gap-4 w-full sm:flex-row sm:w-auto justify-between sm:justify-start">

        @if($paginator->total() > 0)
            <div class="w-full sm:w-auto min-w-[140px] text-sm">
                <x-form.input.select
                    wire:model.live="perPage"
                    :label="__('labels.tables.per_page')"
                    :options="$this->optionsPerPage"
                    name="perPage"
                />
            </div>
        @endif

        <div class="text-sm text-gray-600 dark:text-gray-400 text-center sm:text-left">
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

    </div>

    <div class="w-full sm:w-auto flex justify-center sm:justify-end">
        {{ $paginator->links() }}
    </div>

</div>
