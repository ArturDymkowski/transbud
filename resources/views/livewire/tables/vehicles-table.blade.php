<x-tables.card :createRoute="route('vehicles.create')">
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
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($vehicles as $vehicle)
                <tr wire:key="vehicle-row-{{ $vehicle->id }}">
                    <x-tables.td>
                        <x-form.input.checkbox name="check_{{ $vehicle->id }}" value="{{ $vehicle->id }}" x-model="selected" wire:key="checkbox-{{ $vehicle->id }}"/>
                    </x-tables.td>
                    <x-tables.td>{{ $vehicle->id }}</x-tables.td>
                    <x-tables.td>{{ $vehicle->registration_number }}</x-tables.td>
                    <x-tables.td>{{ $vehicle->vin }}</x-tables.td>
                    <x-tables.td>{{ $vehicle->type?->label() ?? '-' }}</x-tables.td>
                    <x-tables.td>{{ $vehicle->technical_inspection_expiry_date ?? '-' }}</x-tables.td>
                    <x-tables.td>{{ $vehicle->insurance_expiry_date ?? '-' }}</x-tables.td>
                    <x-tables.td>{{ $vehicle->tachograph_inspection_expiry_date ?? '-' }}</x-tables.td>
                    <x-tables.td>
                        <x-form.input.toggle wire:change="toggleActive({{ $vehicle->id }})"
                                             name="{{ $vehicle->id }}" :isActive="$vehicle->is_active" wire:key="toggle-{{ $vehicle->id }}"/>
                    </x-tables.td>
                    <x-tables.td class="flex space-x-2">
                        <x-tables.action-show :route="route('vehicles.show', $vehicle->id)"/>
                        <x-tables.action-edit :route="route('vehicles.edit', $vehicle->id)"/>
                        <x-tables.action-delete
                            wire:click="deleteVehicle({{ $vehicle->id }})"
                            :confirm="__('vehicles.confirm_delete_vehicle')"
                        />
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
