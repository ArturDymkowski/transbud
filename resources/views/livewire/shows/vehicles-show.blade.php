<!-- Podgląd -->
<div>

    <div class="grid grid-cols-1 gap-6">

        <!-- Sekcja: Informacje podstawowe -->
        <x-form.section title="{{ __('vehicles.basic_info') }}">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-3">

                <div class="col-span-1">
                    <x-form.input.text-input name="vehicleData.registration_number"
                                             label="{{ __('vehicles.registration_number') }}"
                                             :value="$vehicleData['registration_number'] ?? ''"
                                             disabled/>
                </div>

                <div class="col-span-1">
                    <x-form.input.text-input name="vehicleData.vin"
                                             label="{{ __('vehicles.vin') }}"
                                             :value="$vehicleData['vin'] ?? ''"
                                             disabled/>
                </div>

                <div class="col-span-1">
                    <x-form.input.select name="vehicleData.type"
                                         label="{{ __('vehicles.type.type') }}"
                                         :default="$vehicleData['type']?->value ?? ''"
                                         :options="\App\Enums\VehicleTypeEnum::getOptions()"
                                         disabled/>
                </div>

            </div>
        </x-form.section>

        <!-- Sekcja: Terminy ważności -->
        <x-form.section title="{{ __('vehicles.inspections') }}">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <x-form.input.date-picker name="vehicleData.technical_inspection_expiry_date"
                                          label="{{ __('vehicles.technical_inspection_expiry_date') }}"
                                          defaultDate="{{ $vehicleData['technical_inspection_expiry_date'] ?? '' }}"
                                          disabled/>

                <x-form.input.date-picker name="vehicleData.insurance_expiry_date"
                                          label="{{ __('vehicles.insurance_expiry_date') }}"
                                          defaultDate="{{ $vehicleData['insurance_expiry_date'] ?? '' }}"
                                          disabled/>

                <x-form.input.date-picker name="vehicleData.tachograph_inspection_expiry_date"
                                          label="{{ __('vehicles.tachograph_inspection_expiry_date') }}"
                                          defaultDate="{{ $vehicleData['tachograph_inspection_expiry_date'] ?? '' }}"
                                          disabled/>
            </div>
        </x-form.section>

        <!-- Sekcja: Informacje dodatkowe -->
        <x-form.section title="{{ __('vehicles.additional_notes') }}">
            <x-form.input.text-input type="textarea" name="vehicleData.additional_notes"
                                     :value="$vehicleData['additional_notes'] ?? ''" disabled/>
        </x-form.section>

    </div>

    <x-tables.show-footer-actions :indexRoute="route('vehicles.index')" :editRoute="route('vehicles.edit', $vehicle)"/>

</div>
