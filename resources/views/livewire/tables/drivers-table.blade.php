<div>
    @if($vehicle)
        <div class="flex w-full justify-end mb-4">
            <x-ui.button wire:click="openAssignModal" size="sm" variant="primary">
                {{ __('vehicles.assign_driver') }}
            </x-ui.button>
        </div>

        <x-ui.modal wire:model="showAssignModal" class="max-w-md p-6 lg:p-8">
            <h4 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                {{ __('vehicles.assign_driver') }}
            </h4>

            <form wire:submit="assignDriver">
                <x-form.errors-summary/>

                <x-form.input.searchable-select
                    name="selectedDriverId"
                    label="{{ __('drivers.singular_model_label') }}"
                    wire:model="selectedDriverId"
                    :options="$this->assignableDriverOptions"
                    required="true"
                />

                <x-form.actions/>
            </form>
        </x-ui.modal>
    @endif

    <x-tables.card :createRoute="$vehicle ? null : route('drivers.create')">
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

            <!-- Country -->
            <x-form.input.select :label="__('labels.address.country')" :options="$this->countryOptions" name="country" wire:model.live="country"/>

            <!-- Driving license expiry date -->
            <div class="flex flex-col">
                <x-form.input.date-picker name="drivingLicenseExpiryDateFrom"
                                          label="{{ __('drivers.driving_license_expiry_date') }}"
                                          wire:model.live="drivingLicenseExpiryDateFrom"
                                          placeholder="{{ __('labels.general.from') }}"/>

                <span class="text-center text-gray-700 dark:text-gray-400">-</span>

                <x-form.input.date-picker name="drivingLicenseExpiryDateTo"
                                          label=""
                                          wire:model.live="drivingLicenseExpiryDateTo"
                                          placeholder="{{ __('labels.general.to') }}"/>
            </div>

            <!-- Identity card expiry date -->
            <div class="flex flex-col">
                <x-form.input.date-picker name="identityCardExpiryDateFrom"
                                          label="{{ __('drivers.identity_card_expiry_date') }}"
                                          wire:model.live="identityCardExpiryDateFrom"
                                          placeholder="{{ __('labels.general.from') }}"/>

                <span class="text-center text-gray-700 dark:text-gray-400">-</span>

                <x-form.input.date-picker name="identityCardExpiryDateTo"
                                          label=""
                                          wire:model.live="identityCardExpiryDateTo"
                                          placeholder="{{ __('labels.general.to') }}"/>
            </div>
        </x-tables.filter-bar>
    </x-slot:header>

    <div class="max-w-full px-5 overflow-x-auto" x-data="tableSelection(@entangle('selected'), @entangle('idsOnPage'), {{ json_encode($drivers->pluck('id')) }})">
        <x-tables.selection-bar deleteAction="deleteSelected" :confirmMessage="$vehicle ? __('vehicles.confirm_remove_driver_assignments') : __('labels.tables.confirm_delete_selected')"/>
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

                <x-tables.th-sort
                    field="name"
                    :label="__('drivers.name')"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                @unless($vehicle)
                    <th scope="col"
                        class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                        {{ __('drivers.phone') }}
                    </th>

                    <th scope="col"
                        class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                        {{ __('drivers.pesel') }}
                    </th>

                    <th scope="col"
                        class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                        {{ __('labels.address.address') }}
                    </th>

                    <x-tables.th-sort
                        field="driving_license_expiry_date"
                        :label="__('drivers.driving_license_expiry_date')"
                        :sortField="$sortField"
                        :sortDirection="$sortDirection"
                    />

                    <x-tables.th-sort
                        field="identity_card_expiry_date"
                        :label="__('drivers.identity_card_expiry_date')"
                        :sortField="$sortField"
                        :sortDirection="$sortDirection"
                    />

                    <x-tables.th-sort
                        field="is_active"
                        :label="__('labels.tables.active')"
                        :sortField="$sortField"
                        :sortDirection="$sortDirection"
                    />
                @endunless

                <th scope="col"
                    class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                    {{ __('labels.tables.actions') }}
                </th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($drivers as $driver)
                <tr wire:key="driver-row-{{ $driver->id }}">
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <x-form.input.checkbox name="check_{{ $driver->id }}" value="{{ $driver->id }}" x-model="selected" wire:key="checkbox-{{ $driver->id }}"/>
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $driver->id }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $driver->name ?? '-' }}</div>
                    </td>
                    @unless($vehicle)
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $driver->phone ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $driver->pesel ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div
                                class="text-sm text-gray-500 dark:text-gray-400">{!! $driver->fullAddress ?? '-' !!}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div
                                class="text-sm text-gray-500 dark:text-gray-400">{{ $driver->driving_license_expiry_date ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div
                                class="text-sm text-gray-500 dark:text-gray-400">{{ $driver->identity_card_expiry_date ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <x-form.input.toggle wire:change="toggleActive({{ $driver->id }})"
                                                     name="{{ $driver->id }}" :isActive="$driver->is_active" wire:key="toggle-{{ $driver->id }}"/>
                            </div>
                        </td>
                    @endunless
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400 flex space-x-2">
                            <x-ui.tooltip :text="__('labels.tables.edit')">
                                <a href="{{ route('drivers.edit', $driver->id) }}" wire:navigate>
                                    <x-heroicon-o-pencil-square class="w-6 h-6 hover:text-green-500"/>
                                </a>
                            </x-ui.tooltip>
                            @if($vehicle)
                                <x-ui.tooltip :text="__('vehicles.remove_driver_assignment')">
                                    <button type="button"
                                            wire:click="deleteDriver({{ $driver->id }})"
                                            wire:confirm="{{ __('vehicles.confirm_remove_driver_assignment') }}"
                                    >
                                        <x-heroicon-o-link-slash class="w-6 h-6 hover:text-red-500"/>
                                    </button>
                                </x-ui.tooltip>
                            @else
                                <x-ui.tooltip :text="__('labels.tables.delete')">
                                    <button type="button"
                                            wire:click="deleteDriver({{ $driver->id }})"
                                            wire:confirm="{{ __('drivers.confirm_delete_driver') }}"
                                    >
                                        <x-heroicon-o-trash class="w-6 h-6 hover:text-red-500"/>
                                    </button>
                                </x-ui.tooltip>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <x-slot:footer>
        <x-tables.pagination-footer :paginator="$drivers"/>
    </x-slot:footer>
</x-tables.card>
</div>
