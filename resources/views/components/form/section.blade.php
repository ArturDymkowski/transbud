@props(['title'])

<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950">
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
            {{ $title }}
        </h2>
    </div>

    {{ $slot }}
</div>
