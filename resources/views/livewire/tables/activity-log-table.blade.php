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

                <x-tables.th>{{ __('activity_log.subject') }}</x-tables.th>

                <x-tables.th>{{ __('activity_log.causer') }}</x-tables.th>

                <x-tables.th>{{ __('activity_log.changes') }}</x-tables.th>

                <x-tables.th-sort
                    field="created_at"
                    :label="__('activity_log.created_at')"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 max-md:block max-md:divide-y-0 max-md:space-y-4">
            @forelse($activities as $activity)
                @php
                    $changes = $activity->attribute_changes;
                    $newValues = $changes?->get('attributes') ?? [];
                    $oldValues = $changes?->get('old') ?? [];
                    $changedKeys = array_unique([...array_keys($newValues), ...array_keys($oldValues)]);
                @endphp
                <tr wire:key="activity-log-row-{{ $activity->id }}" class="max-md:block max-md:space-y-3 max-md:rounded-xl max-md:border max-md:border-gray-200 max-md:p-4 max-md:divide-y max-md:divide-gray-100 dark:max-md:border-gray-700 dark:max-md:divide-gray-800">
                    <x-tables.td label="ID">{{ $activity->id }}</x-tables.td>
                    <x-tables.td :label="__('activity_log.resource')">
                        {{ $activity->log_name ? __('activity_log.resources.'.$activity->log_name) : '-' }}
                    </x-tables.td>
                    <x-tables.td :label="__('activity_log.event')">
                        <x-ui.status-badge :color="$this->eventColor($activity->event)">
                            {{ __('activity_log.events.'.$activity->event) }}
                        </x-ui.status-badge>
                    </x-tables.td>
                    <x-tables.td :label="__('activity_log.subject')">
                        {{ $this->subjectLabel($activity->subject, $activity->subject_id) }}
                    </x-tables.td>
                    <x-tables.td :label="__('activity_log.causer')">
                        {{ $activity->causer?->name ?? __('activity_log.system') }}
                    </x-tables.td>
                    <x-tables.td :label="__('activity_log.changes')" class="max-w-sm">
                        @if($changedKeys)
                            <ul class="space-y-1 text-xs text-gray-500 dark:text-gray-400">
                                @foreach($changedKeys as $key)
                                    <li>
                                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $key }}:</span>
                                        @if(array_key_exists($key, $oldValues))
                                            <span class="line-through">{{ $this->formatChangeValue($oldValues[$key]) }}</span> →
                                        @endif
                                        @if(array_key_exists($key, $newValues))
                                            <span>{{ $this->formatChangeValue($newValues[$key]) }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-gray-400">{{ __('activity_log.no_changes') }}</span>
                        @endif
                    </x-tables.td>
                    <x-tables.td :label="__('activity_log.created_at')">{{ $activity->created_at?->format('Y-m-d H:i:s') }}</x-tables.td>
                </tr>
            @empty
                <tr class="max-md:block">
                    <td colspan="7" class="max-md:block px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
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
