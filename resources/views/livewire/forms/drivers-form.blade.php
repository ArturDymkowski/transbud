<!-- Formularz -->
<form wire:submit="save">

    @if ($errors->any())
        <div
            wire:key="errors-{{ uniqid() }}"
            x-data
            x-init="$el.scrollIntoView({ behavior: 'smooth', block: 'start' })"
            class="scroll-mt-24 p-4 mb-4 text-sm text-red-800 bg-red-100 rounded-lg dark:bg-red-900 dark:text-red-200">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6">

        <!-- Sekcja: Informacje podstawowe -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950">
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                    {{ __('labels.general.basic_info') }}
                </h2>
            </div>

            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-3">

                <div class="col-span-1">
                    <x-form.input.text-input name="driverData.name"
                                             label="{{ __('drivers.name') }}"
                                             required="true"
                                             wire:model="driverData.name"
                    />
                </div>

                <div class="col-span-1">
                    <x-form.input.text-input name="driverData.phone"
                                             label="{{ __('drivers.phone') }}"
                                             wire:model="driverData.phone"
                                             required="true"
                    />
                </div>

                <div class="col-span-1">
                    <x-form.input.text-input name="driverData.pesel"
                                             label="{{ __('drivers.pesel') }}"
                                             wire:model="driverData.pesel"
                                             required="true"
                    />
                </div>

            </div>
        </div>

        <!-- SEKCJA PRAWO JAZDY -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950 mb-6">
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">{{ __('drivers.driving_license') }}</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <x-form.input.text-input name="driverData.driving_license_number"
                                             label="{{ __('drivers.driving_license_nr') }}"
                                             required="true"
                                             wire:model="driverData.driving_license_number"/>
                </div>
                <div>
                    <x-form.input.date-picker name="driverData.driving_license_expiry_date"
                                              label="{{ __('drivers.driving_license_expiry_date') }}"
                                              required="true"
                                              wire:model="driverData.driving_license_expiry_date"
                                              defaultDate="{{ $driverData['driving_license_expiry_date'] ?? '' }}"/>
                </div>

                <!-- Przód dokumentu -->
                <x-form.document-upload
                    field="driving_license_document_front"
                    label="{{ __('drivers.document_front') }}"
                    :file="$driverData['driving_license_document_front'] ?? null"
                    :existing-media-id="$this->existingMedia['driving_license_document_front'] ?? null"/>

                <!-- Tył dokumentu -->
                <x-form.document-upload
                    field="driving_license_document_back"
                    label="{{ __('drivers.document_back') }}"
                    :file="$driverData['driving_license_document_back'] ?? null"
                    :existing-media-id="$this->existingMedia['driving_license_document_back'] ?? null"/>
            </div>
        </div>

        <!-- SEKCJA DOWÓD OSOBISTY -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950 mb-6">
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">{{ __('drivers.identity_card') }}</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <x-form.input.text-input name="driverData.identity_card_number"
                                             label="{{ __('drivers.identity_card_nr') }}"
                                             required="true"
                                             wire:model="driverData.identity_card_number"/>
                </div>
                <div>
                    <x-form.input.date-picker name="driverData.identity_card_expiry_date"
                                              label="{{ __('drivers.identity_card_expiry_date') }}"
                                              required="true"
                                              wire:model="driverData.identity_card_expiry_date"
                                              defaultDate="{{ $driverData['identity_card_expiry_date'] ?? '' }}"/>
                </div>

                <!-- Przód dokumentu -->
                <x-form.document-upload
                    field="identity_card_document_front"
                    label="{{ __('drivers.document_front') }}"
                    :file="$driverData['identity_card_document_front'] ?? null"
                    :existing-media-id="$this->existingMedia['identity_card_document_front'] ?? null"/>

                <!-- Tył dokumentu -->
                <x-form.document-upload
                    field="identity_card_document_back"
                    label="{{ __('drivers.document_back') }}"
                    :file="$driverData['identity_card_document_back'] ?? null"
                    :existing-media-id="$this->existingMedia['identity_card_document_back'] ?? null"/>
            </div>
        </div>

        <!-- Sekcja: Adres -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950">
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                    {{ __('labels.address.address') }}
                </h2>
            </div>

            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">

                <x-form.input.select name="driverData.country" label="{{ __('labels.address.country') }}" wire:model="driverData.country"
                                     :options="\App\Enums\CountriesEnum::getOptions()"/>

                <x-form.input.text-input name="driverData.zipcode" label="{{ __('labels.address.zipcode') }}"
                                         wire:model="driverData.zipcode"/>

                <x-form.input.text-input name="driverData.city" label="{{ __('labels.address.city') }}" wire:model="driverData.city"/>
                <x-form.input.text-input name="driverData.street" label="{{ __('labels.address.street') }}" wire:model="driverData.street"/>
                <x-form.input.text-input name="driverData.house_nr" label="{{ __('labels.address.house_nr') }}"
                                         wire:model="driverData.house_nr"/>
                <x-form.input.text-input name="driverData.apartment_nr" label="{{ __('labels.address.apartment_nr') }}"
                                         wire:model="driverData.apartment_nr"/>

            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950">
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                    {{ __('labels.general.extra_info') }}
                </h2>
            </div>

            <x-form.input.text-input type="textarea" name="driverData.extra_info"
                                     wire:model="driverData.extra_info"/>
        </div>

    </div>

    <div class="flex items-center justify-end w-full gap-3 mt-6">
        <x-ui.button @click="open = false" class="w-full" size="sm" variant="outline">{{ __('labels.general.close') }}</x-ui.button>
        <x-ui.button type="submit" class="w-full" size="sm" variant="primary">{{ __('labels.general.save') }}</x-ui.button>
    </div>

</form>
