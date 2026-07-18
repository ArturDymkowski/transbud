<x-tables.card>
    <x-slot:header>
        <x-tables.filter-bar searchModel="search">
            <!-- Trashed -->
            <x-form.input.select :label="__('labels.tables.trashed')" :options="$this->trashedOptions" name="trashed" wire:model.live="trashed"/>    
        
            <!-- Active -->
            <x-form.input.select :label="__('labels.tables.active')" :options="[
				         '' => __('labels.tables.all'),
                         0 => __('labels.tables.no'),
                         1 => __('labels.tables.yes')
			]" name="isActive" wire:model.live="isActive"/>

            <!-- Technical inspection expiry date -->
            <div class="flex flex-col">
                <x-form.input.date-picker name="technicalInspectionExpiryDateFrom"
                                          label="{{ __('vehicles.technical_inspection_expiry_date') }}"
                                          wire:model.live="technicalInspectionExpiryDateFrom"
                                          placeholder="{{ __('labels.general.from') }}"/>

                <span class="text-center text-gray-700 dark:text-gray-400">-</span>

                <x-form.input.date-picker name="technicalInspectionExpiryDateTo"
                                          label=""
                                          wire:model.live="technicalInspectionExpiryDateTo"
                                          placeholder="{{ __('labels.general.to') }}"/>
            </div>

            <!-- Insurance expiry date -->
            <div class="flex flex-col">
                <x-form.input.date-picker name="insuranceExpiryDateFrom"
                                          label="{{ __('vehicles.insurance_expiry_date') }}"
                                          wire:model.live="insuranceExpiryDateFrom"
                                          placeholder="{{ __('labels.general.from') }}"/>

                <span class="text-center text-gray-700 dark:text-gray-400">-</span>

                <x-form.input.date-picker name="insuranceExpiryDateTo"
                                          label=""
                                          wire:model.live="insuranceExpiryDateTo"
                                          placeholder="{{ __('labels.general.to') }}"/>
            </div>

            <!-- Tachograph inspection expiry date -->
            <div class="flex flex-col">
                <x-form.input.date-picker name="tachographInspectionExpiryDateFrom"
                                          label="{{ __('vehicles.tachograph_inspection_expiry_date') }}"
                                          wire:model.live="tachographInspectionExpiryDateFrom"
                                          placeholder="{{ __('labels.general.from') }}"/>

                <span class="text-center text-gray-700 dark:text-gray-400">-</span>

                <x-form.input.date-picker name="tachographInspectionExpiryDateTo"
                                          label=""
                                          wire:model.live="tachographInspectionExpiryDateTo"
                                          placeholder="{{ __('labels.general.to') }}"/>
            </div>
        </x-tables.filter-bar>
    </x-slot:header>

    <div class="max-w-full px-5 overflow-x-auto" x-data="tableSelection(@entangle('selected'), @entangle('idsOnPage'), {{ json_encode($vehicles->pluck('id')) }})">
        <x-tables.selection-bar deleteAction="deleteSelected" :confirmMessage="__('labels.tables.confirm_delete_selected')"/>
        <x-tables.filter-badges :filters="$this->activeFilters"/>

        <table class="min-w-full">
            <thead>
            <tr class="border-gray-200 border-y dark:border-gray-700">
                <th scope="col"
                    class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                    <x-form.input.checkbox
                        name="selectAll"
                        @click="togglePage"
                        x-bind:checked="isAllPageSelected()"
                    />
                </th>

                <x-tables.th-sort
                    field="id"
                    label="ID"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <th scope="col"
                    class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                    {{ __('vehicles.registration_number') }}
                </th>

                <th scope="col"
                    class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                    {{ __('vehicles.vin') }}
                </th>

                <x-tables.th-sort
                    field="type"
                    :label="__('vehicles.type.type')"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
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

                                <th scope="col"
                    class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                    {{ __('labels.tables.actions') }}
                </th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($vehicles as $vehicle)
                <tr wire:key="vehicle-row-{{ $vehicle->id }}">
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <x-form.input.checkbox name="check_{{ $vehicle->id }}" value="{{ $vehicle->id }}" x-model="selected" wire:key="checkbox-{{ $vehicle->id }}"/>
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $vehicle->id }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $vehicle->registration_number }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $vehicle->vin }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $vehicle->type?->label() ?? '-' }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $vehicle->technical_inspection_expiry_date ?? '-' }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $vehicle->insurance_expiry_date ?? '-' }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $vehicle->tachograph_inspection_expiry_date ?? '-' }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <x-form.input.toggle wire:change="toggleActive({{ $vehicle->id }})"
                                                 name="{{ $vehicle->id }}" :isActive="$vehicle->is_active" wire:key="toggle-{{ $vehicle->id }}"/>
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400 flex space-x-2">
                            <x-ui.tooltip :text="__('labels.tables.edit')">
                                <a href="#" wire:navigate>
                                    <x-heroicon-o-pencil-square class="w-6 h-6 hover:text-green-500"/>
                                </a>
                            </x-ui.tooltip>
                            <x-ui.tooltip :text="__('labels.tables.delete')">
                                <button type="button">
                                    <x-heroicon-o-trash class="w-6 h-6 hover:text-red-500"/>
                                </button>
                            </x-ui.tooltip>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <x-slot:footer>
        <x-tables.pagination-footer :paginator="$vehicles"/>
    </x-slot:footer>
</x-tables.card>
