<x-tables.card>
    <x-slot:header>
        <x-tables.filter-bar searchModel="search" :placeholder="__('activity_log.search_placeholder')">
            <x-form.input.select :label="__('activity_log.resource')" :options="$this->logNameOptions" name="logName" wire:model.live="logName"/>
            <x-form.input.select :label="__('activity_log.event')" :options="$this->eventOptions" name="event" wire:model.live="event"/>
        </x-tables.filter-bar>
    </x-slot:header>

    <div class="max-w-full px-5 overflow-x-auto">
        <x-tables.filter-badges :filters="$this->activeFilters"/>

        <table class="min-w-full max-md:block">
            <thead class="max-md:hidden">
            <tr class="border-gray-200 border-y dark:border-gray-700">
                <x-tables.th-sort
                    field="id"
                    label="ID"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <x-tables.th>{{ __('activity_log.resource') }}</x-tables.th>

                <x-tables.th>{{ __('activity_log.event') }}</x-tables.th>

                <x-tables.th>{{ __('activity_log.causer') }}</x-tables.th>

                <x-tables.th-sort
                    field="created_at"
                    :label="__('activity_log.created_at')"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <x-tables.th>{{ __('labels.tables.actions') }}</x-tables.th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 max-md:block max-md:divide-y-0 max-md:space-y-4">
            @forelse($activities as $activity)
                <x-tables.tr wire:key="activity-log-row-{{ $activity->id }}" :route="route('activity-log.show', $activity)">
                    <x-tables.td label="ID">{{ $activity->id }}</x-tables.td>
                    <x-tables.td :label="__('activity_log.resource')">
                        {{ $this->subjectLabel($activity) }}
                    </x-tables.td>
                    <x-tables.td :label="__('activity_log.event')">
                        <x-ui.status-badge :color="$this->eventColor($activity->event)">
                            {{ __('activity_log.events.'.$activity->event) }}
                        </x-ui.status-badge>
                    </x-tables.td>
                    <x-tables.td :label="__('activity_log.causer')">
                        {{ $this->causerLabel($activity) }}
                    </x-tables.td>
                    <x-tables.td :label="__('activity_log.created_at')">
                        <x-ui.status-badge color="#12b76a">
                            {{ $activity->created_at?->format('Y-m-d H:i:s') }}
                        </x-ui.status-badge>
                    </x-tables.td>
                    <x-tables.td :label="__('labels.tables.actions')" class="flex space-x-2">
                        <x-tables.action-show :route="route('activity-log.show', $activity)"/>
                    </x-tables.td>
                </x-tables.tr>
            @empty
                <tr class="max-md:block">
                    <td colspan="6" class="max-md:block px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        {{ __('labels.tables.no_results') }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <x-slot:footer>
        <x-tables.pagination-footer :paginator="$activities"/>
    </x-slot:footer>
</x-tables.card>
