@props(['cancelRoute'])

<form wire:submit="save" {{ $attributes }}>

    <x-form.errors-summary/>

    <div class="grid grid-cols-1 gap-6">
        {{ $slot }}
    </div>

    <x-form.actions :cancelRoute="$cancelRoute"/>

</form>
