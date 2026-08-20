@props(['options', 'active'])

<div class="mb-3 flex items-center gap-2">
    @foreach ($options as $value => $label)
        <x-ui.button
            type="button"
            size="sm"
            :variant="$active === $value ? 'primary' : 'outline'"
            wire:click="setResourceType('{{ $value }}')"
        >
            {{ $label }}
        </x-ui.button>
    @endforeach
</div>
