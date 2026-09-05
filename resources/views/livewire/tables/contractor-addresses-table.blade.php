<div>
    @if($contractor && ! $readonly)
        <div class="flex w-full justify-end mb-4">
            <x-ui.button wire:click="openCreateModal" size="sm" variant="primary">
                {{ __('labels.tables.create') }}
            </x-ui.button>
        </div>

        @include('livewire.modals.contractor-address-create-modal')
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
            @can('contractor-addresses.delete')
                <x-tables.selection-bar deleteAction="deleteSelected" :confirmMessage="__('labels.tables.confirm_delete_selected')"/>
            @endcan
        @endunless
        <x-tables.filter-badges :filters="$this->activeFilters"/>

        <table class="min-w-full max-md:block">
            <thead class="max-md:hidden">
            <tr class="border-gray-200 border-y dark:border-gray-700">
                @unless($readonly)
                    @can('contractor-addresses.delete')
                        <x-tables.th>
                            <x-form.input.checkbox
                                name="selectAll"
                                @click="togglePage"
                                x-bind:checked="isAllPageSelected()"
                            />
                        </x-tables.th>
                    @endcan
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
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 max-md:block max-md:divide-y-0 max-md:space-y-4">
            @foreach($addresses as $address)
                <tr wire:key="address-row-{{ $address->id }}" class="max-md:block max-md:space-y-3 max-md:rounded-xl max-md:border max-md:border-gray-200 max-md:p-4 max-md:divide-y max-md:divide-gray-100 dark:max-md:border-gray-700 dark:max-md:divide-gray-800">
                    @unless($readonly)
                        @can('contractor-addresses.delete')
                            <x-tables.td>
                                <x-form.input.checkbox name="check_{{ $address->id }}" value="{{ $address->id }}" x-model="selected" wire:key="checkbox-{{ $address->id }}"/>
                            </x-tables.td>
                        @endcan
                    @endunless
                    <x-tables.td label="ID">{{ $address->id }}</x-tables.td>
                    @unless($contractor)
                        <x-tables.td :label="__('address_book.contractor')">
                            <a href="{{ route('contractors.edit', $address->contractor_id) }}" wire:navigate class="hover:text-brand-500 hover:underline">
                                {{ $address->contractor->name ?? '-' }}
                            </a>
                        </x-tables.td>
                    @endunless
                    <x-tables.td :label="__('labels.address.address')">
                        @forelse($address->fullAddressLines as $addressLine)
                            {{ $addressLine }}@if(!$loop->last)<br>@endif
                        @empty
                            -
                        @endforelse
                    </x-tables.td>
                    <x-tables.td :label="__('labels.tables.active')">
                        @if($address->trashed())
                            <span class="text-gray-400">-</span>
                        @elseif($readonly)
                            <x-ui.status-badge :color="$address->is_active ? '#12b76a' : '#f04438'">
                                {{ $address->is_active ? __('labels.tables.yes') : __('labels.tables.no') }}
                            </x-ui.status-badge>
                        @else
                            <x-form.input.toggle wire:change="toggleActive({{ $address->id }})"
                                                 name="{{ $address->id }}" :isActive="$address->is_active" wire:key="toggle-{{ $address->id }}"
                                                 :disabled="! auth()->user()?->can('contractor-addresses.edit')"/>
                        @endif
                    </x-tables.td>
                    <x-tables.td class="flex space-x-2">
                        <x-tables.action-show :route="route('contractor-addresses.show', $address->id)"/>
                        @unless($readonly)
                            @if($address->trashed())
                                @can('contractor-addresses.edit')
                                    <x-tables.action-restore
                                        wire:click="restoreAddress({{ $address->id }})"
                                        :confirm="__('labels.tables.confirm_restore')"
                                    />
                                @endcan
                                @can('contractor-addresses.delete')
                                    <x-tables.action-delete
                                        wire:click="forceDeleteAddress({{ $address->id }})"
                                        :confirm="__('labels.tables.confirm_force_delete')"
                                        :label="__('labels.tables.force_delete')"
                                    >
                                        <x-heroicon-o-x-mark class="w-6 h-6 hover:text-red-500"/>
                                    </x-tables.action-delete>
                                @endcan
                            @else
                                @can('contractor-addresses.edit')
                                    <x-tables.action-edit :route="route('contractor-addresses.edit', $address->id)"/>
                                @endcan
                                @can('contractor-addresses.delete')
                                    <x-tables.action-delete
                                        wire:click="deleteAddress({{ $address->id }})"
                                        :confirm="__('address_book.confirm_delete_address')"
                                    />
                                @endcan
                            @endif
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
