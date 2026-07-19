@if ($errors->any())
    <div
        wire:key="errors-{{ uniqid() }}"
        x-data
        x-init="$el.scrollIntoView({ behavior: 'smooth', block: 'start' })"
        class="scroll-mt-24 p-4 mb-4 text-sm text-red-800 bg-red-100 rounded-lg dark:bg-red-900 dark:text-red-200">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
