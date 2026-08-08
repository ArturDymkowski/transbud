@props(['date'])

@php
    $color = \App\Helpers\ExpiryHelper::color($date);
@endphp

@if(! $date)
    -
@elseif($color)
    <x-ui.status-badge :color="$color">
        {{ $date }}
    </x-ui.status-badge>
@else
    {{ $date }}
@endif
