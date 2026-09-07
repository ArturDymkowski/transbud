@props(['route' => null, 'id' => null])

<tr
    {{ $attributes->class([
        'max-md:block max-md:space-y-3 max-md:rounded-xl max-md:border max-md:border-gray-200 max-md:p-4 max-md:divide-y max-md:divide-gray-100 dark:max-md:border-gray-700 dark:max-md:divide-gray-800',
        'transition-colors hover:bg-gray-50 dark:hover:bg-white/[0.03]',
        'cursor-pointer' => $route,
    ]) }}
    @if($id !== null)
        x-bind:class="{ 'bg-gray-50 dark:bg-white/[0.03]': selected.includes('{{ $id }}') }"
    @endif
    @if($route)
        @click="$event.target.closest('a, button, input, label') || Livewire.navigate('{{ $route }}')"
    @endif
>
    {{ $slot }}
</tr>
