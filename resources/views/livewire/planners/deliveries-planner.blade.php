<div style="width: 0; min-width: 100%;">
    <x-planner.toolbar :date="$date" />

    <x-planner.resource-type-switcher
        :options="\App\Support\Planner\PlannerResourceType::getOptions()"
        :active="$resourceType"
    />

    <x-planner.resource-filter
        name="planner-resource-filter-{{ $resourceType }}"
        label="{{ $this->resourceTypeEnum->label() }}"
        :options="$this->resourceOptions"
        :allLabel="$this->resourceTypeEnum->allLabel()"
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
                    wire:key="planner-row-{{ $resourceType }}-{{ $resource->id }}"
                />
            @empty
                <div class="p-6 text-sm text-gray-400 dark:text-gray-500">
                    {{ __('deliveries.planner.no_resources', ['type' => $this->resourceTypeEnum->label()]) }}
                </div>
            @endforelse
        </div>
    </div>
</div>
