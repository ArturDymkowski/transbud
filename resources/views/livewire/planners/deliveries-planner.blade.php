{{-- width:0 + min-width:100% forces this to always be exactly its parent's width instead
     of being stretched by the wide, horizontally-scrollable grid below (a flex ancestor
     higher up the shared layout has no min-width:0, so without this the grid's intrinsic
     width would otherwise bubble up and make the whole page scroll horizontally). --}}
<div style="width: 0; min-width: 100%;">
    <x-planner.toolbar :date="$date" />

    {{-- .live is required here: the underlying entangle() only pushes to the server on
         the next Livewire request otherwise, so unchecking/checking a driver would update
         the dropdown label but never actually re-filter the grid. --}}
    <x-planner.resource-filter
        label="{{ __('deliveries.planner.drivers_filter_label') }}"
        :options="$this->resourceOptions"
        :allLabel="__('deliveries.planner.all_drivers')"
        wire:model.live="selectedResourceIds"
    />

    <div class="overflow-auto rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900" style="height: clamp(320px, 65vh, 720px);">
        <div style="width: {{ 160 + ($this->hours->count() - 1) * $pxPerHour }}px;">
            <x-planner.header :hours="$this->hours" :pxPerHour="$pxPerHour" />

            @forelse ($this->resources as $resource)
                <x-planner.row
                    :resource="$resource"
                    :events="$this->eventsByResource->get($resource->id)"
                    :hours="$this->hours"
                    :pxPerHour="$pxPerHour"
                    wire:key="planner-row-{{ $resource->id }}"
                />
            @empty
                <div class="p-6 text-sm text-gray-400 dark:text-gray-500">
                    {{ __('deliveries.planner.no_resources') }}
                </div>
            @endforelse
        </div>
    </div>
</div>
