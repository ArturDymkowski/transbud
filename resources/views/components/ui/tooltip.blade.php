@props(['text'])

<div x-data="{ open: false }"
     class="relative flex justify-center"
     @mouseenter="open = true"
     @mouseleave="open = false"
     @focusin="open = true"
     @focusout="open = false">

    {{ $slot }}

    <div x-show="open"
         x-cloak
         class="absolute z-50 bottom-full mb-2 px-2 py-1 text-xs text-white bg-gray-800 rounded whitespace-nowrap left-1/2 -translate-x-1/2"
    >
        {{ $text }}
    </div>
</div>
