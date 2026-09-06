<div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <div class="lg:col-span-2">
            <x-form.section title="{{ __('activity_log.singular_model_label') }}">
                <div class="grid grid-cols-1 gap-y-5">
                    <x-form.input.text-input name="causer"
                                             label="{{ __('activity_log.causer') }}"
                                             :value="$this->causerLabel($activity)"
                                             disabled/>

                    <x-form.input.text-input name="element"
                                             label="{{ __('activity_log.element') }}"
                                             :value="$this->subjectLabel($activity)"
                                             disabled/>

                    <x-form.input.text-input type="textarea"
                                              name="description"
                                              label="{{ __('activity_log.description') }}"
                                              :value="$this->description()"
                                              disabled/>
                </div>
            </x-form.section>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950">
            <div class="space-y-5">
                <div>
                    <p class="text-xs font-medium tracking-wide text-gray-400 uppercase">{{ __('activity_log.type') }}</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white/90">
                        {{ $activity->log_name ? __('activity_log.resources.'.$activity->log_name) : '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium tracking-wide text-gray-400 uppercase">{{ __('activity_log.event') }}</p>
                    <p class="mt-1">
                        <x-ui.status-badge :color="$this->eventColor($activity->event)">
                            {{ __('activity_log.events.'.$activity->event) }}
                        </x-ui.status-badge>
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium tracking-wide text-gray-400 uppercase">{{ __('activity_log.event_date') }}</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white/90">
                        {{ $activity->created_at?->format('Y-m-d H:i:s') }}
                    </p>
                </div>
            </div>
        </div>

    </div>

    <div class="mt-6">
        <x-form.section title="{{ __('activity_log.changes') }}">
            @if(empty($oldValues) && empty($newValues))
                <p class="text-sm text-gray-400">{{ __('activity_log.no_changes') }}</p>
            @else
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('activity_log.before_change') }}</h3>
                        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-800">
                            <table class="min-w-full">
                                <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-800">
                                    <x-tables.th>{{ __('activity_log.key') }}</x-tables.th>
                                    <x-tables.th>{{ __('activity_log.value') }}</x-tables.th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                @forelse($oldValues as $key => $value)
                                    <tr wire:key="old-value-{{ $key }}">
                                        <x-tables.td class="font-mono text-xs">{{ $key }}</x-tables.td>
                                        <x-tables.td>{{ $this->formatChangeValue($value) }}</x-tables.td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-4 py-4 text-center text-sm text-gray-400">-</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div>
                        <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('activity_log.after_change') }}</h3>
                        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-800">
                            <table class="min-w-full">
                                <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-800">
                                    <x-tables.th>{{ __('activity_log.key') }}</x-tables.th>
                                    <x-tables.th>{{ __('activity_log.value') }}</x-tables.th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                @forelse($newValues as $key => $value)
                                    <tr wire:key="new-value-{{ $key }}">
                                        <x-tables.td class="font-mono text-xs">{{ $key }}</x-tables.td>
                                        <x-tables.td>{{ $this->formatChangeValue($value) }}</x-tables.td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-4 py-4 text-center text-sm text-gray-400">-</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </x-form.section>
    </div>

    <x-tables.show-footer-actions :indexRoute="route('activity-log.index')" :editRoute="null"/>

</div>
