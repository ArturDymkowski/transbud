<!-- Podgląd -->
<div>

    <div class="grid grid-cols-1 gap-6">

        <!-- Sekcja: Informacje podstawowe -->
        <x-form.section title="{{ __('deliveries.basic_info') }}">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-3">

                <div class="col-span-1">
                    <x-form.input.text-input name="deliveryData.number"
                                             label="{{ __('deliveries.number') }}"
                                             :value="$delivery->number"
                                             disabled/>
                </div>

                <div class="col-span-1">
                    <x-form.input.text-input name="deliveryData.contractor"
                                             label="{{ __('deliveries.contractor') }}"
                                             :value="$delivery->contractor->name ?? '-'"
                                             disabled/>
                </div>

                <div class="col-span-1">
                    <x-form.input.text-input name="deliveryData.contractor_address"
                                             label="{{ __('deliveries.contractor_address') }}"
                                             :value="str_replace('<br>', ', ', $delivery->contractorAddress->fullAddress ?? '-')"
                                             disabled/>
                </div>

                <div class="col-span-1">
                    <x-form.input.text-input name="deliveryData.loading_address"
                                             label="{{ __('deliveries.loading_address') }}"
                                             :value="$delivery->loading_address"
                                             disabled/>
                </div>

                <div class="col-span-1">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        {{ __('deliveries.status.status') }}
                    </label>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $delivery->status->badgeClasses() }}">
                        {{ $delivery->status->label() }}
                    </span>
                </div>

            </div>
        </x-form.section>

        <!-- Sekcja: Zestawy transportowe -->
        <x-form.section title="{{ __('deliveries.transport_set.transport_sets') }}">
            <div class="flex flex-col gap-4">
                @forelse($delivery->transportSets as $transportSet)
                    <div wire:key="show-transport-set-{{ $transportSet->id }}" class="flex flex-col gap-4 p-4 rounded-xl border border-gray-100 dark:border-gray-800">
                        <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-3">
                            <div class="col-span-1">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    {{ __('deliveries.transport_set_status.status') }}
                                </label>
                                {{ $transportSet->status->label() }}
                            </div>

                            <x-form.input.text-input name="transportSet_{{ $transportSet->id }}_driver"
                                                     label="{{ __('deliveries.transport_set.driver') }}"
                                                     :value="$transportSet->driver->name ?? '-'"
                                                     disabled/>

                            <x-form.input.text-input name="transportSet_{{ $transportSet->id }}_vehicle"
                                                     label="{{ __('vehicles.type.tractor') }}"
                                                     :value="$transportSet->vehicle->registration_number ?? '-'"
                                                     disabled/>

                            <x-form.input.text-input name="transportSet_{{ $transportSet->id }}_trailer"
                                                     label="{{ __('vehicles.type.trailer') }}"
                                                     :value="$transportSet->trailer->registration_number ?? '-'"
                                                     disabled/>

                            <x-form.input.text-input name="transportSet_{{ $transportSet->id }}_loading_at"
                                                     label="{{ __('deliveries.transport_set.loading_at') }}"
                                                     :value="$transportSet->loading_at?->format('Y-m-d H:i') ?? '-'"
                                                     disabled/>

                            <x-form.input.text-input name="transportSet_{{ $transportSet->id }}_unloading_at"
                                                     label="{{ __('deliveries.transport_set.unloading_at') }}"
                                                     :value="$transportSet->unloading_at?->format('Y-m-d H:i') ?? '-'"
                                                     disabled/>
                        </div>

                        <div class="max-w-full overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                <tr class="border-gray-200 border-y dark:border-gray-700">
                                    <x-tables.th>{{ __('deliveries.goods.good') }}</x-tables.th>
                                    <x-tables.th>{{ __('deliveries.goods.unit') }}</x-tables.th>
                                    <x-tables.th>{{ __('deliveries.goods.quantity') }}</x-tables.th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($transportSet->goods as $good)
                                    <tr wire:key="show-good-{{ $good->id }}">
                                        <x-tables.td>{{ $good->good->name ?? '-' }}</x-tables.td>
                                        <x-tables.td>{{ $good->unit->name ?? '-' }}</x-tables.td>
                                        <x-tables.td>{{ $good->quantity }}</x-tables.td>
                                    </tr>
                                @empty
                                    <tr>
                                        <x-tables.td>-</x-tables.td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">-</p>
                @endforelse
            </div>
        </x-form.section>

    </div>

    <x-tables.show-footer-actions :indexRoute="route('deliveries.index')" :editRoute="route('deliveries.edit', $delivery)"/>

</div>
