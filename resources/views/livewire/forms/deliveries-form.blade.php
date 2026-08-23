<div>
    @include('livewire.modals.delivery-address-create-modal')

    <!-- Formularz -->
    <x-form.wrapper :cancelRoute="route('deliveries.index')">

        <!-- Sekcja: Informacje podstawowe -->
        <x-form.section title="{{ __('deliveries.basic_info') }}">
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
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        {{ __('deliveries.contractor_address') }} <x-form.input.required-star/>
                    </label>
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <x-form.input.select name="deliveryData.contractor_address_id"
                                                 wire:model="deliveryData.contractor_address_id"
                                                 :options="$this->contractorAddressOptions($deliveryData['contractor_id'] ?? null)"
                                                 required="true"/>
                        </div>
                        <x-ui.tooltip :text="__('address_book.create_title')">
                            <button type="button"
                                    wire:click="openCreateAddressModal"
                                    @disabled(empty($deliveryData['contractor_id']))
                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg border border-gray-300 text-gray-500 hover:text-brand-500 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:text-gray-400">
                                <x-heroicon-o-plus class="w-5 h-5"/>
                            </button>
                        </x-ui.tooltip>
                    </div>
                </div>

                <div class="col-span-1">
                    <x-form.input.text-input name="deliveryData.loading_address"
                                             label="{{ __('deliveries.loading_address') }}"
                                             required="true"
                                             wire:model="deliveryData.loading_address"
                    />
                </div>

                <div class="col-span-1" x-data="quantityInput(@entangle('deliveryData.freight_amount'))">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        {{ __('deliveries.freight_amount') }}
                    </label>
                    <input type="text"
                           inputmode="decimal"
                           name="deliveryData.freight_amount"
                           :value="display"
                           x-on:input="onInput($event)"
                           @class([
                               'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30',
                               'border-gray-300 dark:border-gray-700' => !$errors->has('deliveryData.freight_amount'),
                               'border-red-300 dark:border-red-700' => $errors->has('deliveryData.freight_amount'),
                           ])/>
                    @error('deliveryData.freight_amount')
                        <div class="mt-1 text-xs text-red-500">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-span-1">
                    <x-form.input.select name="deliveryData.currency"
                                         label="{{ __('deliveries.currency') }}"
                                         wire:model="deliveryData.currency"
                                         :options="\App\Enums\CurrencyEnum::getOptions()"
                                         required="true"/>
                </div>

            </div>
        </x-form.section>

        <!-- Sekcja: Zestawy transportowe -->
        <x-form.section title="{{ __('deliveries.transport_set.transport_sets') }}">
            <div class="flex flex-col gap-4">
                @foreach($transportSetsData as $index => $transportSet)
                    @php($fieldsRequired = (int) ($transportSet['status'] ?? 0) !== \App\Enums\DeliveryTransportSetStatusEnum::DRAFT->value)

                    <div wire:key="transport-set-row-{{ $index }}" class="relative flex flex-col gap-5 p-4 pr-16 rounded-xl border border-gray-100 dark:border-gray-800">
                        <button type="button" wire:click="removeTransportSetRow({{ $index }})"
                                title="{{ __('deliveries.transport_set.remove') }}"
                                class="absolute top-3 right-3 flex h-11 w-11 items-center justify-center text-gray-400 hover:text-red-500 dark:text-gray-500">
                            <x-heroicon-o-trash class="w-5 h-5"/>
                        </button>

                        <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-3">
                            <div class="col-span-1">
                                <x-form.input.select name="transportSetsData.{{ $index }}.status"
                                                     label="{{ __('deliveries.transport_set_status.status') }}"
                                                     wire:model.live="transportSetsData.{{ $index }}.status"
                                                     :options="\App\Enums\DeliveryTransportSetStatusEnum::getOptions()"
                                                     required="true"/>
                            </div>

                            <div class="col-span-1">
                                <x-form.input.searchable-select name="transportSetsData.{{ $index }}.driver_id"
                                                     label="{{ __('deliveries.transport_set.driver') }}"
                                                     wire:model.live="transportSetsData.{{ $index }}.driver_id"
                                                     :options="$this->driverOptionsFor($index)"
                                                     :required="$fieldsRequired"/>
                            </div>

                            <div class="col-span-1">
                                <x-form.input.searchable-select name="transportSetsData.{{ $index }}.vehicle_id"
                                                     label="{{ __('vehicles.type.tractor') }}"
                                                     wire:model.live="transportSetsData.{{ $index }}.vehicle_id"
                                                     :options="$this->tractorOptionsFor($index)"
                                                     :required="$fieldsRequired"/>
                            </div>

                            <div class="col-span-1">
                                <x-form.input.searchable-select name="transportSetsData.{{ $index }}.trailer_id"
                                                     label="{{ __('vehicles.type.trailer') }}"
                                                     wire:model.live="transportSetsData.{{ $index }}.trailer_id"
                                                     :options="$this->trailerOptionsFor($index)"
                                                     :required="$fieldsRequired"/>
                            </div>

                            <div class="col-span-1" wire:key="loading-at-wrap-{{ $index }}-{{ $fieldsRequired ? 1 : 0 }}">
                                <x-form.input.date-picker name="transportSetsData.{{ $index }}.loading_at"
                                                          label="{{ __('deliveries.transport_set.loading_at') }}"
                                                          :required="$fieldsRequired"
                                                          enableTime="true"
                                                          dateFormat="Y-m-d H:i"
                                                          wire:model="transportSetsData.{{ $index }}.loading_at"
                                                          defaultDate="{{ $transportSet['loading_at'] ?? '' }}"/>
                            </div>

                            <div class="col-span-1" wire:key="unloading-at-wrap-{{ $index }}-{{ $fieldsRequired ? 1 : 0 }}">
                                <x-form.input.date-picker name="transportSetsData.{{ $index }}.unloading_at"
                                                          label="{{ __('deliveries.transport_set.unloading_at') }}"
                                                          :required="$fieldsRequired"
                                                          enableTime="true"
                                                          dateFormat="Y-m-d H:i"
                                                          wire:model="transportSetsData.{{ $index }}.unloading_at"
                                                          defaultDate="{{ $transportSet['unloading_at'] ?? '' }}"/>
                            </div>
                        </div>

                        <!-- Sekcja: Towary (w ramach zestawu transportowego) -->
                        <div class="flex flex-col gap-4 pt-2 border-t border-gray-100 dark:border-gray-800">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                {{ __('deliveries.goods.goods') }}
                            </h4>

                            @foreach(($transportSet['goods'] ?? []) as $goodIndex => $good)
                                @php($unitOptions = $this->goodUnitOptions($good['good_id'] ?? null))

                                <div wire:key="good-row-{{ $index }}-{{ $goodIndex }}" class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-3 p-4 rounded-xl border border-gray-100 dark:border-gray-800">
                                    <div class="col-span-1">
                                        <x-form.input.select name="transportSetsData.{{ $index }}.goods.{{ $goodIndex }}.good_id"
                                                             label="{{ __('deliveries.goods.good') }}"
                                                             wire:model.live="transportSetsData.{{ $index }}.goods.{{ $goodIndex }}.good_id"
                                                             :options="$this->goodOptions"
                                                             required="true"/>
                                    </div>

                                    <div class="col-span-1">
                                        @if(count($unitOptions) > 1)
                                            <x-form.input.select name="transportSetsData.{{ $index }}.goods.{{ $goodIndex }}.unit_id"
                                                                 label="{{ __('deliveries.goods.unit') }}"
                                                                 wire:model="transportSetsData.{{ $index }}.goods.{{ $goodIndex }}.unit_id"
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
                                            <div class="flex-1" x-data="quantityInput(@entangle('transportSetsData.'.$index.'.goods.'.$goodIndex.'.quantity'))">
                                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                    {{ __('deliveries.goods.quantity') }} <x-form.input.required-star/>
                                                </label>
                                                <input type="text"
                                                       inputmode="decimal"
                                                       name="transportSetsData.{{ $index }}.goods.{{ $goodIndex }}.quantity"
                                                       :value="display"
                                                       x-on:input="onInput($event)"
                                                       @class([
                                                           'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30',
                                                           'border-gray-300 dark:border-gray-700' => !$errors->has("transportSetsData.{$index}.goods.{$goodIndex}.quantity"),
                                                           'border-red-300 dark:border-red-700' => $errors->has("transportSetsData.{$index}.goods.{$goodIndex}.quantity"),
                                                       ])/>
                                                @error("transportSetsData.{$index}.goods.{$goodIndex}.quantity")
                                                    <div class="mt-1 text-xs text-red-500">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <button type="button" wire:click="removeGoodRow({{ $index }}, {{ $goodIndex }})"
                                                    title="{{ __('deliveries.goods.remove') }}"
                                                    class="flex h-11 w-11 shrink-0 items-center justify-center text-gray-400 hover:text-red-500 dark:text-gray-500">
                                                <x-heroicon-o-trash class="w-5 h-5"/>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div>
                                <x-ui.button wire:click="addGoodRow({{ $index }})" size="sm" variant="outline">
                                    {{ __('deliveries.goods.add') }}
                                </x-ui.button>
                            </div>
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
            @if($this->existingDocuments->isNotEmpty())
                <ul class="mb-3 flex flex-col gap-2">
                    @foreach($this->existingDocuments as $media)
                        <li wire:key="existing-document-{{ $media->id }}" class="flex items-center justify-between px-4 py-2 rounded-lg border border-gray-100 dark:border-gray-800">
                            <a href="{{ route('delivery-documents.show', $media->id) }}" target="_blank" class="text-sm text-gray-700 dark:text-gray-400 truncate hover:text-brand-500 hover:underline">
                                {{ $media->file_name }}
                            </a>
                            <button type="button" wire:click="deleteDocument({{ $media->id }})" wire:confirm="{{ __('deliveries.confirm_delete_document') }}" wire:loading.attr="disabled" class="text-gray-400 hover:text-red-500" title="{{ __('labels.general.delete_document') }}">
                                <x-heroicon-o-trash class="w-5 h-5"/>
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif

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
</div>
