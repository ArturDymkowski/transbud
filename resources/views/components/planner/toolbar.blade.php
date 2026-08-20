@props(['date'])

<div class="flex items-center justify-between gap-4 mb-4">
    <div class="flex items-center gap-2">
        <x-ui.button size="sm" variant="outline" wire:click="previousDay" wire:loading.attr="disabled">
            <x-heroicon-o-chevron-left class="w-4 h-4" />
        </x-ui.button>
        <x-ui.button size="sm" variant="outline" wire:click="goToToday" wire:loading.attr="disabled">
            {{ __('labels.general.today') }}
        </x-ui.button>
        <x-ui.button size="sm" variant="outline" wire:click="nextDay" wire:loading.attr="disabled">
            <x-heroicon-o-chevron-right class="w-4 h-4" />
        </x-ui.button>
    </div>

    <div class="text-base font-semibold text-gray-800 dark:text-white/90">
        {{ \Illuminate\Support\Carbon::parse($date)->format('d.m.Y') }}
    </div>

    <div class="w-[124px]"></div>
</div>
