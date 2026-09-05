<x-tables.card :createRoute="auth()->user()->can('vehicles.create') ? route('vehicles.create') : null">
    <x-slot:header>
        <x-tables.filter-bar searchModel="search">
            <!-- Trashed & Active -->
            <x-tables.filter-trashed-active :trashedOptions="$this->trashedOptions"/>

            <!-- Technical inspection expiry date -->
            <x-tables.filter-date-range prefix="technicalInspectionExpiryDate" :label="__('vehicles.technical_inspection_expiry_date')"/>

            <!-- Insurance expiry date -->
            <x-tables.filter-date-range prefix="insuranceExpiryDate" :label="__('vehicles.insurance_expiry_date')"/>

            <!-- Tachograph inspection expiry date -->
            <x-tables.filter-date-range prefix="tachographInspectionExpiryDate" :label="__('vehicles.tachograph_inspection_expiry_date')"/>
        </x-tables.filter-bar>
    </x-slot:header>

    <div class="max-w-full px-5 overflow-x-auto" x-data="tableSelection(@entangle('selected'), @entangle('idsOnPage'), {{ json_encode($vehicles->pluck('id')) }})">
        @can('vehicles.delete')
            <x-tables.selection-bar deleteAction="deleteSelected" :confirmMessage="__('labels.tables.confirm_delete_selected')"/>
        @endcan
        <x-tables.filter-badges :filters="$this->activeFilters"/>

        <table class="min-w-full max-md:block">
            <thead class="max-md:hidden">
            <tr class="border-gray-200 border-y dark:border-gray-700">
                @can('vehicles.delete')
                    <x-tables.th>
                        <x-form.input.checkbox
                            name="selectAll"
                            @click="togglePage"
                            x-bind:checked="isAllPageSelected()"
                        />
                    </x-tables.th>
                @endcan

                <x-tables.th-sort
                    field="id"
                    label="ID"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <x-tables.th>{{ __('vehicles.registration_number') }}</x-tables.th>

                <x-tables.th>{{ __('vehicles.vin') }}</x-tables.th>

                <x-tables.th-sort
                    field="type"
                    :label="__('vehicles.type.type')"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                    required="true"
                />

                <x-tables.th-sort
                    field="technical_inspection_expiry_date"
                    :label="__('vehicles.technical_inspection_expiry_date')"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <x-tables.th-sort
                    field="insurance_expiry_date"
                    :label="__('vehicles.insurance_expiry_date')"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <x-tables.th-sort
                    field="tachograph_inspection_expiry_date"
                    :label="__('vehicles.tachograph_inspection_expiry_date')"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <x-tables.th-sort
                    field="is_active"
                    :label="__('labels.tables.active')"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <x-tables.th>{{ __('labels.tables.actions') }}</x-tables.th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 max-md:block max-md:divide-y-0 max-md:space-y-4">
            @foreach($vehicles as $vehicle)
                <tr wire:key="vehicle-row-{{ $vehicle->id }}" class="max-md:block max-md:space-y-3 max-md:rounded-xl max-md:border max-md:border-gray-200 max-md:p-4 max-md:divide-y max-md:divide-gray-100 dark:max-md:border-gray-700 dark:max-md:divide-gray-800">
                    @can('vehicles.delete')
                        <x-tables.td>
                            <x-form.input.checkbox name="check_{{ $vehicle->id }}" value="{{ $vehicle->id }}" x-model="selected" wire:key="checkbox-{{ $vehicle->id }}"/>
                        </x-tables.td>
                    @endcan
                    <x-tables.td label="ID">{{ $vehicle->id }}</x-tables.td>
                    <x-tables.td :label="__('vehicles.registration_number')">{{ $vehicle->registration_number }}</x-tables.td>
                    <x-tables.td :label="__('vehicles.vin')">{{ $vehicle->vin }}</x-tables.td>
                    <x-tables.td :label="__('vehicles.type.type')">{{ $vehicle->type?->label() ?? '-' }}</x-tables.td>
                    <x-tables.td :label="__('vehicles.technical_inspection_expiry_date')"><x-ui.expiry-date-badge :date="$vehicle->technical_inspection_expiry_date"/></x-tables.td>
                    <x-tables.td :label="__('vehicles.insurance_expiry_date')"><x-ui.expiry-date-badge :date="$vehicle->insurance_expiry_date"/></x-tables.td>
                    <x-tables.td :label="__('vehicles.tachograph_inspection_expiry_date')"><x-ui.expiry-date-badge :date="$vehicle->tachograph_inspection_expiry_date"/></x-tables.td>
                    <x-tables.td :label="__('labels.tables.active')">
                        @if($vehicle->trashed())
                            <span class="text-gray-400">-</span>
                        @else
                            <x-form.input.toggle wire:change="toggleActive({{ $vehicle->id }})"
                                                 name="{{ $vehicle->id }}" :isActive="$vehicle->is_active" wire:key="toggle-{{ $vehicle->id }}"
                                                 :disabled="! auth()->user()?->can('vehicles.edit')"/>
                        @endif
                    </x-tables.td>
                    <x-tables.td class="flex space-x-2">
                        <x-tables.action-show :route="route('vehicles.show', $vehicle->id)"/>
                        @if($vehicle->trashed())
                            @can('vehicles.edit')
                                <x-tables.action-restore
                                    wire:click="restoreVehicle({{ $vehicle->id }})"
                                    :confirm="__('labels.tables.confirm_restore')"
                                />
                            @endcan
                            @can('vehicles.delete')
                                <x-tables.action-delete
                                    wire:click="forceDeleteVehicle({{ $vehicle->id }})"
                                    :confirm="__('labels.tables.confirm_force_delete')"
                                    :label="__('labels.tables.force_delete')"
                                >
                                    <x-heroicon-o-x-mark class="w-6 h-6 hover:text-red-500"/>
                                </x-tables.action-delete>
                            @endcan
                        @else
                            @can('vehicles.edit')
                                <x-tables.action-edit :route="route('vehicles.edit', $vehicle->id)"/>
                            @endcan
                            @can('vehicles.delete')
                                <x-tables.action-delete
                                    wire:click="deleteVehicle({{ $vehicle->id }})"
                                    :confirm="__('vehicles.confirm_delete_vehicle')"
                                />
                            @endcan
                        @endif
                    </x-tables.td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <x-slot:footer>
        <x-tables.pagination-footer :paginator="$vehicles"/>
    </x-slot:footer>
</x-tables.card>
