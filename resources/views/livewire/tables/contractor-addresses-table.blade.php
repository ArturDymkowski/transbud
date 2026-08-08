<div>
    @if($contractor && ! $readonly)
        <div class="flex w-full justify-end mb-4">
            <x-ui.button wire:click="openCreateModal" size="sm" variant="primary">
                {{ __('labels.tables.create') }}
            </x-ui.button>
        </div>

        <x-ui.modal wire:model="showCreateModal" class="max-w-lg p-6 lg:p-8">
            <h4 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                {{ __('address_book.create_title') }}
            </h4>

            <form wire:submit="createAddress">
                <x-form.errors-summary/>

                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                    <x-form.input.select name="createAddressData.country" label="{{ __('labels.address.country') }}"
                                         wire:model="createAddressData.country"
                                         required="true"
                                         :options="\App\Enums\CountriesEnum::getOptions()"/>

                    <x-form.input.text-input name="createAddressData.zipcode" label="{{ __('labels.address.zipcode') }}"
                                             required="true"
                                             wire:model="createAddressData.zipcode"/>

                    <x-form.input.text-input name="createAddressData.city" label="{{ __('labels.address.city') }}"
                                             required="true"
                                             wire:model="createAddressData.city"/>
                    <x-form.input.text-input name="createAddressData.street" label="{{ __('labels.address.street') }}"
                                             required="true"
                                             wire:model="createAddressData.street"/>
                    <x-form.input.text-input name="createAddressData.house_nr" label="{{ __('labels.address.house_nr') }}"
                                             wire:model="createAddressData.house_nr"/>
                    <x-form.input.text-input name="createAddressData.apartment_nr" label="{{ __('labels.address.apartment_nr') }}"
                                             wire:model="createAddressData.apartment_nr"/>
                </div>

                <x-form.actions/>
            </form>
        </x-ui.modal>
    @endif

    <x-tables.card :createRoute="$contractor ? null : route('contractor-addresses.create')">
    <x-slot:header>
        <x-tables.filter-bar searchModel="search">
            <!-- Trashed & Active -->
            <x-tables.filter-trashed-active :trashedOptions="$this->trashedOptions"/>

            <!-- Country -->
            <x-form.input.select :label="__('labels.address.country')" :options="$this->countryOptions" name="country" wire:model.live="country"/>
        </x-tables.filter-bar>
    </x-slot:header>

    <div class="max-w-full px-5 overflow-x-auto" x-data="tableSelection(@entangle('selected'), @entangle('idsOnPage'), {{ json_encode($addresses->pluck('id')) }})">
        @unless($readonly)
            <x-tables.selection-bar deleteAction="deleteSelected" :confirmMessage="__('labels.tables.confirm_delete_selected')"/>
        @endunless
        <x-tables.filter-badges :filters="$this->activeFilters"/>

        <table class="min-w-full">
            <thead>
            <tr class="border-gray-200 border-y dark:border-gray-700">
                @unless($readonly)
                    <x-tables.th>
                        <x-form.input.checkbox
                            name="selectAll"
                            @click="togglePage"
                            x-bind:checked="isAllPageSelected()"
                        />
                    </x-tables.th>
                @endunless

                <x-tables.th-sort
                    field="id"
                    label="ID"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                @unless($contractor)
                    <x-tables.th-sort
                        field="contractor_name"
                        :label="__('address_book.contractor')"
                        :sortField="$sortField"
                        :sortDirection="$sortDirection"
                    />
                @endunless

                <x-tables.th>{{ __('labels.address.address') }}</x-tables.th>

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
            @foreach($addresses as $address)
                <tr wire:key="address-row-{{ $address->id }}">
                    @unless($readonly)
                        <x-tables.td>
                            <x-form.input.checkbox name="check_{{ $address->id }}" value="{{ $address->id }}" x-model="selected" wire:key="checkbox-{{ $address->id }}"/>
                        </x-tables.td>
                    @endunless
                    <x-tables.td>{{ $address->id }}</x-tables.td>
                    @unless($contractor)
                        <x-tables.td>
                            <a href="{{ route('contractors.edit', $address->contractor_id) }}" wire:navigate class="hover:text-brand-500 hover:underline">
                                {{ $address->contractor->name ?? '-' }}
                            </a>
                        </x-tables.td>
                    @endunless
                    <x-tables.td>{!! $address->fullAddress ?? '-' !!}</x-tables.td>
                    <x-tables.td>
                        @if($readonly)
                            <x-ui.status-badge :color="$address->is_active ? '#12b76a' : '#f04438'">
                                {{ $address->is_active ? __('labels.tables.yes') : __('labels.tables.no') }}
                            </x-ui.status-badge>
                        @else
                            <x-form.input.toggle wire:change="toggleActive({{ $address->id }})"
                                                 name="{{ $address->id }}" :isActive="$address->is_active" wire:key="toggle-{{ $address->id }}"/>
                        @endif
                    </x-tables.td>
                    <x-tables.td class="flex space-x-2">
                        <x-tables.action-show :route="route('contractor-addresses.show', $address->id)"/>
                        @unless($readonly)
                            <x-tables.action-edit :route="route('contractor-addresses.edit', $address->id)"/>
                            <x-tables.action-delete
                                wire:click="deleteAddress({{ $address->id }})"
                                :confirm="__('address_book.confirm_delete_address')"
                            />
                        @endunless
                    </x-tables.td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <x-slot:footer>
        <x-tables.pagination-footer :paginator="$addresses"/>
    </x-slot:footer>
</x-tables.card>
</div>
