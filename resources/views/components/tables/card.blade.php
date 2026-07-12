@props(['createRoute' => null])

<div>
    @if($createRoute)
        <div class="flex w-full justify-end mb-4">
            <x-ui.button><a href="{{ $createRoute }}">{{ __('labels.tables.create') }}</a></x-ui.button>
        </div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
        {{ $header ?? '' }}
        <div class="overflow-hidden">
            {{ $slot }}
        </div>
        {{ $footer ?? '' }}
    </div>
</div>
