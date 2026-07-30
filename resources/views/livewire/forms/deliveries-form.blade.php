<!-- Formularz -->
<x-form.wrapper :cancelRoute="route('deliveries.index')">

    <!-- Sekcja: Informacje podstawowe -->
    <x-form.section title="{{ __('deliveries.basic_info') }}">
        <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-3">

            <div class="col-span-1">
                <x-form.input.text-input name="deliveryData.number"
                                         label="{{ __('deliveries.number') }}"
                                         required="true"
                                         wire:model="deliveryData.number"
                />
            </div>

            <div class="col-span-1">
                <x-form.input.select name="deliveryData.status"
                                     label="{{ __('deliveries.status.status') }}"
                                     wire:model="deliveryData.status"
                                     :options="\App\Enums\DeliveryStatusEnum::getOptions()"
                                     required="true"/>
            </div>

            <div class="col-span-1"></div>

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
    </x-form.section>

    <!-- Sekcja: Towary -->
    <x-form.section title="{{ __('deliveries.goods.goods') }}">
        <div class="flex flex-col gap-4">
            @foreach($goodsData as $index => $good)
                @php($unitOptions = $this->goodUnitOptions($good['good_id'] ?? null))

                <div wire:key="good-row-{{ $index }}" class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-4 items-start p-4 rounded-xl border border-gray-100 dark:border-gray-800">
                    <div class="col-span-1">
                        <x-form.input.select name="goodsData.{{ $index }}.good_id"
                                             label="{{ __('deliveries.goods.good') }}"
                                             wire:model.live="goodsData.{{ $index }}.good_id"
                                             :options="$this->goodOptions"
                                             required="true"/>
                    </div>

                    <div class="col-span-1">
                        @if(count($unitOptions) > 1)
                            <x-form.input.select name="goodsData.{{ $index }}.unit_id"
                                                 label="{{ __('deliveries.goods.unit') }}"
                                                 wire:model="goodsData.{{ $index }}.unit_id"
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
                        <x-form.input.text-input name="goodsData.{{ $index }}.quantity"
                                                 label="{{ __('deliveries.goods.quantity') }}"
                                                 required="true"
                                                 wire:model="goodsData.{{ $index }}.quantity"
                        />
                    </div>

                    <div class="col-span-1 flex items-end h-11">
                        <x-ui.tooltip :text="__('deliveries.goods.remove')">
                            <button type="button" wire:click="removeGoodRow({{ $index }})" class="flex items-center justify-center w-11 h-11 rounded-lg border border-gray-200 dark:border-gray-800 hover:text-red-500">
                                <x-heroicon-o-trash class="w-5 h-5"/>
                            </button>
                        </x-ui.tooltip>
                    </div>
                </div>
            @endforeach

            <div>
                <x-ui.button wire:click="addGoodRow" size="sm" variant="outline">
                    {{ __('deliveries.goods.add') }}
                </x-ui.button>
            </div>
        </div>
    </x-form.section>

    <!-- Sekcja: Zestawy transportowe -->
    <x-form.section title="{{ __('deliveries.transport_set.transport_sets') }}">
        <div class="flex flex-col gap-4">
            @foreach($transportSetsData as $index => $transportSet)
                <div wire:key="transport-set-row-{{ $index }}" class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-3 items-start p-4 rounded-xl border border-gray-100 dark:border-gray-800">
                    <div class="col-span-1">
                        <x-form.input.select name="transportSetsData.{{ $index }}.driver_id"
                                             label="{{ __('deliveries.transport_set.driver') }}"
                                             wire:model="transportSetsData.{{ $index }}.driver_id"
                                             :options="$this->driverOptions"
                                             required="true"/>
                    </div>

                    <div class="col-span-1">
                        <x-form.input.select name="transportSetsData.{{ $index }}.vehicle_id"
                                             label="{{ __('vehicles.type.tractor') }}"
                                             wire:model="transportSetsData.{{ $index }}.vehicle_id"
                                             :options="$this->tractorOptions"
                                             required="true"/>
                    </div>

                    <div class="col-span-1">
                        <x-form.input.select name="transportSetsData.{{ $index }}.trailer_id"
                                             label="{{ __('vehicles.type.trailer') }}"
                                             wire:model="transportSetsData.{{ $index }}.trailer_id"
                                             :options="$this->trailerOptions"
                                             required="true"/>
                    </div>

                    <div class="col-span-1">
                        <x-form.input.date-picker name="transportSetsData.{{ $index }}.loading_at"
                                                  label="{{ __('deliveries.transport_set.loading_at') }}"
                                                  required="true"
                                                  enableTime="true"
                                                  dateFormat="Y-m-d H:i"
                                                  wire:model="transportSetsData.{{ $index }}.loading_at"
                                                  defaultDate="{{ $transportSet['loading_at'] ?? '' }}"/>
                    </div>

                    <div class="col-span-1">
                        <x-form.input.date-picker name="transportSetsData.{{ $index }}.unloading_at"
                                                  label="{{ __('deliveries.transport_set.unloading_at') }}"
                                                  required="true"
                                                  enableTime="true"
                                                  dateFormat="Y-m-d H:i"
                                                  wire:model="transportSetsData.{{ $index }}.unloading_at"
                                                  defaultDate="{{ $transportSet['unloading_at'] ?? '' }}"/>
                    </div>

                    <div class="col-span-1">
                        <x-form.input.select name="transportSetsData.{{ $index }}.status"
                                             label="{{ __('deliveries.transport_set_status.status') }}"
                                             wire:model="transportSetsData.{{ $index }}.status"
                                             :options="\App\Enums\DeliveryTransportSetStatusEnum::getOptions()"
                                             required="true"/>
                    </div>

                    <div class="col-span-1">
                        <x-ui.tooltip :text="__('deliveries.transport_set.remove')">
                            <button type="button" wire:click="removeTransportSetRow({{ $index }})" class="flex items-center justify-center w-11 h-11 rounded-lg border border-gray-200 dark:border-gray-800 hover:text-red-500">
                                <x-heroicon-o-trash class="w-5 h-5"/>
                            </button>
                        </x-ui.tooltip>
                    </div>
                </div>
            @endforeach

            <div>
                <x-ui.button wire:click="addTransportSetRow" size="sm" variant="outline">
                    {{ __('deliveries.transport_set.add') }}
                </x-ui.button>
            </div>
        </div>
    </x-form.section>

    <!-- Sekcja: Dokumenty -->
    <x-form.section title="{{ __('deliveries.documents') }}">
        <x-form.input.file-input name="newDocuments" :label="__('deliveries.documents')" wire:model="newDocuments" multiple accept=".jpg,.jpeg,.png,.webp,.pdf"/>

        @if(count($newDocuments))
            <ul class="mt-3 flex flex-col gap-2">
                @foreach($newDocuments as $index => $file)
                    <li wire:key="new-document-{{ $index }}" class="flex items-center justify-between px-4 py-2 rounded-lg border border-gray-100 dark:border-gray-800">
                        <span class="text-sm text-gray-700 dark:text-gray-400 truncate">{{ $file->getClientOriginalName() }}</span>
                        <button type="button" wire:click="removeNewDocument({{ $index }})" class="text-gray-400 hover:text-red-500">
                            <x-heroicon-o-trash class="w-5 h-5"/>
                        </button>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-form.section>

</x-form.wrapper>
