<div>
    <div wire:ignore x-data="deliveriesCalendar()" x-init="init()" x-destroy="destroy()">
        <div x-ref="calendarEl"></div>
    </div>

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
                            <x-form.input.select name="transportSetData.driver_id"
                                                 label="{{ __('deliveries.transport_set.driver') }}"
                                                 wire:model.live="transportSetData.driver_id"
                                                 :options="$this->driverOptions"
                                                 :required="$fieldsRequired"/>
                        </div>

                        <div class="col-span-1">
                            <x-form.input.select name="transportSetData.vehicle_id"
                                                 label="{{ __('vehicles.type.tractor') }}"
                                                 wire:model="transportSetData.vehicle_id"
                                                 :options="$this->tractorOptions"
                                                 :required="$fieldsRequired"/>
                        </div>

                        <div class="col-span-1">
                            <x-form.input.select name="transportSetData.trailer_id"
                                                 label="{{ __('vehicles.type.trailer') }}"
                                                 wire:model="transportSetData.trailer_id"
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

                <!-- Sekcja: Towary -->
                <div class="flex flex-col gap-4 pt-2 border-t border-gray-100 dark:border-gray-800">
                    <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        {{ __('deliveries.goods.goods') }}
                    </h5>

                    @foreach(($transportSetData['goods'] ?? []) as $goodIndex => $good)
                        @php($unitOptions = $this->goodUnitOptions($good['good_id'] ?? null))

                        <div wire:key="good-row-{{ $goodIndex }}" class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-3 p-4 rounded-xl border border-gray-100 dark:border-gray-800">
                            <div class="col-span-1">
                                <x-form.input.select name="transportSetData.goods.{{ $goodIndex }}.good_id"
                                                     label="{{ __('deliveries.goods.good') }}"
                                                     wire:model.live="transportSetData.goods.{{ $goodIndex }}.good_id"
                                                     :options="$this->goodOptions"
                                                     required="true"/>
                            </div>

                            <div class="col-span-1">
                                @if(count($unitOptions) > 1)
                                    <x-form.input.select name="transportSetData.goods.{{ $goodIndex }}.unit_id"
                                                         label="{{ __('deliveries.goods.unit') }}"
                                                         wire:model="transportSetData.goods.{{ $goodIndex }}.unit_id"
                                                         :options="['' => __('labels.general.not_selected')] + $unitOptions"
                                                         required="true"/>
                                @else
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        {{ __('deliveries.goods.unit') }}
                                    </label>
                                    <div class="h-11 flex items-center px-4 text-sm text-gray-500 dark:text-gray-400 rounded-lg border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-white/5">
                                        {{ $unitOptions[array_key_first($unitOptions)] ?? '-' }}
                                    </div>
                                @endif
                            </div>

                            <div class="col-span-1">
                                <div class="flex items-end gap-2">
                                    <div class="flex-1" x-data="quantityInput(@entangle('transportSetData.goods.'.$goodIndex.'.quantity'))">
                                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                            {{ __('deliveries.goods.quantity') }} <x-form.input.required-star/>
                                        </label>
                                        <input type="text"
                                               inputmode="decimal"
                                               name="transportSetData.goods.{{ $goodIndex }}.quantity"
                                               :value="display"
                                               x-on:input="onInput($event)"
                                               @class([
                                                   'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30',
                                                   'border-gray-300 dark:border-gray-700' => !$errors->has("transportSetData.goods.{$goodIndex}.quantity"),
                                                   'border-red-300 dark:border-red-700' => $errors->has("transportSetData.goods.{$goodIndex}.quantity"),
                                               ])/>
                                        @error("transportSetData.goods.{$goodIndex}.quantity")
                                            <div class="mt-1 text-xs text-red-500">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="button" wire:click="removeGoodRow({{ $goodIndex }})"
                                            title="{{ __('deliveries.goods.remove') }}"
                                            class="flex h-11 w-11 shrink-0 items-center justify-center text-gray-400 hover:text-red-500 dark:text-gray-500">
                                        <x-heroicon-o-trash class="w-5 h-5"/>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div>
                        <x-ui.button wire:click="addGoodRow" size="sm" variant="outline">
                            {{ __('deliveries.goods.add') }}
                        </x-ui.button>
                    </div>
                </div>

                <x-form.actions/>
            </form>
        @endif
    </x-ui.modal>
</div>
