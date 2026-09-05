<div>
    <!-- Modal: Edycja zestawu transportowego -->
    <x-ui.modal wire:model="isOpen" class="max-w-3xl p-6 lg:p-8">
        <h4 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
            {{ __('deliveries.edit_transport_set') }}
        </h4>

        @if($transportSetId)
            <form wire:submit="save">
                <x-form.errors-summary/>

                @php($fieldsRequired = (int) ($transportSetData['status'] ?? 0) !== \App\Enums\DeliveryTransportSetStatusEnum::DRAFT->value)

                <!-- Sekcja: Informacje o dostawie -->
                <div class="mb-6">
                    <h5 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        {{ __('deliveries.basic_info') }}
                    </h5>

                    <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-3">
                        <div class="col-span-1">
                            <x-form.input.text-input name="deliveryData.number"
                                                     label="{{ __('deliveries.number') }}"
                                                     readonly
                                                     wire:model="deliveryData.number"
                            />
                        </div>

                        <div class="col-span-1">
                            <x-form.input.select name="deliveryData.contractor_id"
                                                 label="{{ __('deliveries.contractor') }}"
                                                 wire:model.live="deliveryData.contractor_id"
                                                 :options="$this->contractorOptions"
                                                 required="true"/>
                        </div>

                        <div class="col-span-1">
                            <x-form.input.select name="deliveryData.contractor_address_id"
                                                 label="{{ __('deliveries.contractor_address') }}"
                                                 wire:model="deliveryData.contractor_address_id"
                                                 :options="$this->contractorAddressOptions($deliveryData['contractor_id'] ?? null)"
                                                 required="true"/>
                        </div>

                        <div class="col-span-1">
                            <x-form.input.text-input name="deliveryData.loading_address"
                                                     label="{{ __('deliveries.loading_address') }}"
                                                     required="true"
                                                     wire:model="deliveryData.loading_address"
                            />
                        </div>
                    </div>
                </div>

                <!-- Sekcja: Zestaw transportowy -->
                <div class="mb-6">
                    <h5 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        {{ __('deliveries.transport_set.transport_set') }}
                    </h5>

                    <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-3">
                        <div class="col-span-1">
                            <x-form.input.select name="transportSetData.status"
                                                 label="{{ __('deliveries.transport_set_status.status') }}"
                                                 wire:model.live="transportSetData.status"
                                                 :options="\App\Enums\DeliveryTransportSetStatusEnum::getOptions()"
                                                 required="true"/>
                        </div>

                        <div class="col-span-1">
                            <x-form.input.searchable-select name="transportSetData.driver_id"
                                                 label="{{ __('deliveries.transport_set.driver') }}"
                                                 wire:model.live="transportSetData.driver_id"
                                                 :options="$this->driverOptions"
                                                 :required="$fieldsRequired"/>
                        </div>

                        <div class="col-span-1">
                            <x-form.input.searchable-select name="transportSetData.vehicle_id"
                                                 label="{{ __('vehicles.type.tractor') }}"
                                                 wire:model.live="transportSetData.vehicle_id"
                                                 :options="$this->tractorOptions"
                                                 :required="$fieldsRequired"/>
                        </div>

                        <div class="col-span-1">
                            <x-form.input.searchable-select name="transportSetData.trailer_id"
                                                 label="{{ __('vehicles.type.trailer') }}"
                                                 wire:model.live="transportSetData.trailer_id"
                                                 :options="$this->trailerOptions"
                                                 :required="$fieldsRequired"/>
                        </div>

                        <div class="col-span-1">
                            <x-form.input.date-picker name="transportSetData.loading_at"
                                                      label="{{ __('deliveries.transport_set.loading_at') }}"
                                                      :required="$fieldsRequired"
                                                      enableTime="true"
                                                      dateFormat="Y-m-d H:i"
                                                      wire:model="transportSetData.loading_at"
                                                      defaultDate="{{ $transportSetData['loading_at'] ?? '' }}"/>
                        </div>

                        <div class="col-span-1">
                            <x-form.input.date-picker name="transportSetData.unloading_at"
                                                      label="{{ __('deliveries.transport_set.unloading_at') }}"
                                                      :required="$fieldsRequired"
                                                      enableTime="true"
                                                      dateFormat="Y-m-d H:i"
                                                      wire:model="transportSetData.unloading_at"
                                                      defaultDate="{{ $transportSetData['unloading_at'] ?? '' }}"/>
                        </div>
                    </div>
                </div>

                <!-- Sekcja: Towary (podgląd) -->
                <div class="flex flex-col gap-4 pt-2 border-t border-gray-100 dark:border-gray-800">
                    <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        {{ __('deliveries.goods.goods') }}
                    </h5>

                    <div class="max-w-full overflow-x-auto">
                        <table class="min-w-full max-md:block">
                            <thead class="max-md:hidden">
                            <tr class="border-gray-200 border-y dark:border-gray-700">
                                <x-tables.th>{{ __('deliveries.goods.good') }}</x-tables.th>
                                <x-tables.th>{{ __('deliveries.goods.unit') }}</x-tables.th>
                                <x-tables.th>{{ __('deliveries.goods.quantity') }}</x-tables.th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 max-md:block max-md:divide-y-0 max-md:space-y-4">
                            @forelse(($transportSetData['goods'] ?? []) as $good)
                                <tr wire:key="preview-good-{{ $good['id'] }}" class="max-md:block max-md:space-y-3 max-md:rounded-xl max-md:border max-md:border-gray-200 max-md:p-4 max-md:divide-y max-md:divide-gray-100 dark:max-md:border-gray-700 dark:max-md:divide-gray-800">
                                    <x-tables.td :label="__('deliveries.goods.good')">{{ $this->goodOptions[$good['good_id']] ?? '-' }}</x-tables.td>
                                    <x-tables.td :label="__('deliveries.goods.unit')">{{ $this->goodUnitOptions($good['good_id'])[$good['unit_id']] ?? '-' }}</x-tables.td>
                                    <x-tables.td :label="__('deliveries.goods.quantity')">{{ $good['quantity'] }}</x-tables.td>
                                </tr>
                            @empty
                                <tr class="max-md:block">
                                    <x-tables.td>-</x-tables.td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-center justify-end w-full gap-3 mt-6">
                    <x-ui.button class="w-full" size="sm" variant="outline" :href="route('deliveries.edit', $deliveryId)" wire:navigate>{{ __('deliveries.go_to_edit') }}</x-ui.button>
                    <x-ui.button type="submit" class="w-full" size="sm" variant="primary">{{ __('labels.general.save') }}</x-ui.button>
                </div>
            </form>
        @endif
    </x-ui.modal>
</div>
