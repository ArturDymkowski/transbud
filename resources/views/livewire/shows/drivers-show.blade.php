<!-- Podgląd -->
<div>

    <div class="grid grid-cols-1 gap-6">

        <!-- Sekcja: Informacje podstawowe -->
        <x-form.section title="{{ __('labels.general.basic_info') }}">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-3">

                <div class="col-span-1">
                    <x-form.input.text-input name="driverData.name"
                                             label="{{ __('drivers.name') }}"
                                             :value="$driverData['name'] ?? ''"
                                             disabled/>
                </div>

                <div class="col-span-1">
                    <x-form.input.text-input name="driverData.phone"
                                             label="{{ __('drivers.phone') }}"
                                             :value="$driverData['phone'] ?? ''"
                                             disabled/>
                </div>

                <div class="col-span-1">
                    <x-form.input.text-input name="driverData.pesel"
                                             label="{{ __('drivers.pesel') }}"
                                             :value="$driverData['pesel'] ?? ''"
                                             disabled/>
                </div>

            </div>
        </x-form.section>

        <!-- SEKCJA PRAWO JAZDY -->
        <x-form.section title="{{ __('drivers.driving_license') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <x-form.input.text-input name="driverData.driving_license_number"
                                             label="{{ __('drivers.driving_license_nr') }}"
                                             :value="$driverData['driving_license_number'] ?? ''"
                                             disabled/>
                </div>
                <div>
                    <x-form.input.date-picker name="driverData.driving_license_expiry_date"
                                              label="{{ __('drivers.driving_license_expiry_date') }}"
                                              defaultDate="{{ $driverData['driving_license_expiry_date'] ?? '' }}"
                                              disabled/>
                </div>

                <!-- Przód dokumentu -->
                <x-form.document-preview
                    field="driving_license_document_front"
                    label="{{ __('drivers.document_front') }}"
                    :existing-media-id="$this->existingMedia['driving_license_document_front'] ?? null"/>

                <!-- Tył dokumentu -->
                <x-form.document-preview
                    field="driving_license_document_back"
                    label="{{ __('drivers.document_back') }}"
                    :existing-media-id="$this->existingMedia['driving_license_document_back'] ?? null"/>
            </div>
        </x-form.section>

        <!-- SEKCJA DOWÓD OSOBISTY -->
        <x-form.section title="{{ __('drivers.identity_card') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <x-form.input.text-input name="driverData.identity_card_number"
                                             label="{{ __('drivers.identity_card_nr') }}"
                                             :value="$driverData['identity_card_number'] ?? ''"
                                             disabled/>
                </div>
                <div>
                    <x-form.input.date-picker name="driverData.identity_card_expiry_date"
                                              label="{{ __('drivers.identity_card_expiry_date') }}"
                                              defaultDate="{{ $driverData['identity_card_expiry_date'] ?? '' }}"
                                              disabled/>
                </div>

                <!-- Przód dokumentu -->
                <x-form.document-preview
                    field="identity_card_document_front"
                    label="{{ __('drivers.document_front') }}"
                    :existing-media-id="$this->existingMedia['identity_card_document_front'] ?? null"/>

                <!-- Tył dokumentu -->
                <x-form.document-preview
                    field="identity_card_document_back"
                    label="{{ __('drivers.document_back') }}"
                    :existing-media-id="$this->existingMedia['identity_card_document_back'] ?? null"/>
            </div>
        </x-form.section>

        <!-- SEKCJA PRZYPISANE POJAZDY -->
        <x-form.section title="{{ __('drivers.assigned_vehicles') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <x-form.input.select name="driverData.tractor_id" label="{{ __('vehicles.type.tractor') }}"
                                     :default="$driverData['tractor_id'] ?? ''"
                                     :options="$this->tractorOptions"
                                     disabled/>

                <x-form.input.select name="driverData.trailer_id" label="{{ __('vehicles.type.trailer') }}"
                                     :default="$driverData['trailer_id'] ?? ''"
                                     :options="$this->trailerOptions"
                                     disabled/>
            </div>
        </x-form.section>

        <!-- Sekcja: Adres -->
        <x-form.section title="{{ __('labels.address.address') }}">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">

                <x-form.input.select name="driverData.country" label="{{ __('labels.address.country') }}"
                                     :default="$driverData['country']?->value ?? ''"
                                     :options="\App\Enums\CountriesEnum::getOptions()"
                                     disabled/>

                <x-form.input.text-input name="driverData.zipcode" label="{{ __('labels.address.zipcode') }}"
                                         :value="$driverData['zipcode'] ?? ''" disabled/>

                <x-form.input.text-input name="driverData.city" label="{{ __('labels.address.city') }}"
                                         :value="$driverData['city'] ?? ''" disabled/>
                <x-form.input.text-input name="driverData.street" label="{{ __('labels.address.street') }}"
                                         :value="$driverData['street'] ?? ''" disabled/>
                <x-form.input.text-input name="driverData.house_nr" label="{{ __('labels.address.house_nr') }}"
                                         :value="$driverData['house_nr'] ?? ''" disabled/>
                <x-form.input.text-input name="driverData.apartment_nr" label="{{ __('labels.address.apartment_nr') }}"
                                         :value="$driverData['apartment_nr'] ?? ''" disabled/>

            </div>
        </x-form.section>

        <!-- Sekcja: Informacje dodatkowe -->
        <x-form.section title="{{ __('labels.general.extra_info') }}">
            <x-form.input.text-input type="textarea" name="driverData.extra_info"
                                     :value="$driverData['extra_info'] ?? ''" disabled/>
        </x-form.section>

    </div>

    <x-tables.show-footer-actions :indexRoute="route('drivers.index')" :editRoute="route('drivers.edit', $driver)"/>

</div>
