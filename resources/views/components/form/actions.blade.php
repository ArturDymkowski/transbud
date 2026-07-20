@props(['cancelRoute' => null])

<div class="flex items-center justify-end w-full gap-3 mt-6">
    @if($cancelRoute)
        <x-ui.button class="w-full" size="sm" variant="outline">
            <a href="{{ $cancelRoute }}" wire:navigate>{{ __('labels.general.close') }}</a>
        </x-ui.button>
    @else
        <x-ui.button @click="open = false" class="w-full" size="sm" variant="outline">{{ __('labels.general.close') }}</x-ui.button>
    @endif
    <x-ui.button type="submit" class="w-full" size="sm" variant="primary">{{ __('labels.general.save') }}</x-ui.button>
</div>
