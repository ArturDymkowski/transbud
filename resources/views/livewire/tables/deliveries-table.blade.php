<x-tables.card :createRoute="route('deliveries.create')">
    <x-slot:header>
        <x-tables.filter-bar searchModel="search">
            <!-- Status -->
            <x-form.input.select :label="__('deliveries.status.status')" :options="$this->statusOptions" name="status" wire:model.live="status"/>
        </x-tables.filter-bar>
    </x-slot:header>

    <div class="max-w-full px-5 overflow-x-auto" x-data="tableSelection(@entangle('selected'), @entangle('idsOnPage'), {{ json_encode($deliveries->pluck('id')) }})">
        <x-tables.selection-bar deleteAction="deleteSelected" :confirmMessage="__('labels.tables.confirm_delete_selected')"/>
        <x-tables.filter-badges :filters="$this->activeFilters"/>

        <table class="min-w-full">
            <thead>
            <tr class="border-gray-200 border-y dark:border-gray-700">
                <x-tables.th>
                    <x-form.input.checkbox
                        name="selectAll"
                        @click="togglePage"
                        x-bind:checked="isAllPageSelected()"
                    />
                </x-tables.th>

                <x-tables.th-sort
                    field="id"
                    label="ID"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <x-tables.th-sort
                    field="number"
                    :label="__('deliveries.number')"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <x-tables.th>{{ __('deliveries.loading_address') }}</x-tables.th>

                <x-tables.th-sort
                    field="contractor_name"
                    :label="__('deliveries.contractor')"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <x-tables.th>{{ __('deliveries.contractor_address') }}</x-tables.th>

                <x-tables.th>{{ __('deliveries.transport_set.transport_sets') }}</x-tables.th>

                <x-tables.th-sort
                    field="status"
                    :label="__('deliveries.status.status')"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <x-tables.th>{{ __('deliveries.profitability.margin') }}</x-tables.th>

                <x-tables.th>{{ __('labels.tables.actions') }}</x-tables.th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($deliveries as $delivery)
                <tr wire:key="delivery-row-{{ $delivery->id }}">
                    <x-tables.td>
                        <x-form.input.checkbox name="check_{{ $delivery->id }}" value="{{ $delivery->id }}" x-model="selected" wire:key="checkbox-{{ $delivery->id }}"/>
                    </x-tables.td>
                    <x-tables.td>{{ $delivery->id }}</x-tables.td>
                    <x-tables.td>{{ $delivery->number }}</x-tables.td>
                    <x-tables.td>{{ $delivery->loading_address }}</x-tables.td>
                    <x-tables.td>
                        <a href="{{ route('contractors.edit', $delivery->contractor_id) }}" wire:navigate class="hover:text-brand-500 hover:underline">
                            {{ $delivery->contractor->name ?? '-' }}
                        </a>
                    </x-tables.td>
                    <x-tables.td>
                        @forelse(($delivery->contractorAddress->fullAddressLines ?? []) as $addressLine)
                            {{ $addressLine }}@if(!$loop->last)<br>@endif
                        @empty
                            -
                        @endforelse
                    </x-tables.td>
                    <x-tables.td>{{ $delivery->transport_sets_count }}</x-tables.td>
                    <x-tables.td>
                        <x-ui.status-badge :color="$delivery->status->color()">
                            {{ $delivery->status->label() }}
                        </x-ui.status-badge>
                    </x-tables.td>
                    <x-tables.td>
                        @php($margin = $delivery->marginPercent())
                        <x-ui.status-badge :color="\App\Enums\MarginRatingEnum::fromPercent($margin)->color()">
                            {{ $margin !== null ? number_format($margin, 2, ',', ' ').'%' : '-' }}
                        </x-ui.status-badge>
                        <div class="mt-1 text-xs text-gray-400">
                            {{ \App\Helpers\MoneyHelper::format($delivery->profitAmount(), $delivery->currency) }}
                        </div>
                    </x-tables.td>
                    <x-tables.td class="flex space-x-2">
                        <x-tables.action-show :route="route('deliveries.show', $delivery->id)"/>
                        @can('deliveries.edit')
                            <x-tables.action-edit :route="route('deliveries.edit', $delivery->id)"/>
                        @endcan
                        @can('deliveries.delete')
                            <x-tables.action-delete
                                wire:click="deleteDelivery({{ $delivery->id }})"
                                :confirm="__('deliveries.confirm_delete_delivery')"
                            />
                        @endcan
                    </x-tables.td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <x-slot:footer>
        <x-tables.pagination-footer :paginator="$deliveries"/>
    </x-slot:footer>
</x-tables.card>
