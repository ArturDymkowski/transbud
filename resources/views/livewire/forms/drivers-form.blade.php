{{--<div class="relative w-full max-h-[90vh] overflow-y-auto rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">--}}

    <!-- Formularz -->
    <form wire:submit="save">

        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 bg-red-100 rounded-lg dark:bg-red-900 dark:text-red-200">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6">

            <!-- Sekcja: Dane podstawowe -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                        Dane podstawowe
                    </h2>
                </div>

                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-3">

                    <div class="col-span-1">
                        <x-form.input.text-input name="driverData.name"
                                                 label="Imię i Nazwisko"
                                                 required="true"
                                                 wire:model="driverData.name"
                        />
                    </div>

                    <div class="col-span-1">
                        <x-form.input.text-input name="driverData.phone"
                                                 label="Telefon"
                                                 wire:model="driverData.phone"
                                                 required="true"
                        />
                    </div>

                    <div class="col-span-1">
                        <x-form.input.text-input name="driverData.pesel"
                                                 label="PESEL"
                                                 wire:model="driverData.pesel"
                                                 required="true"
                        />
                    </div>

                </div>
            </div>

            <!-- SEKCJA PRAWO JAZDY -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950 mb-6">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Prawo jazdy</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <x-form.input.text-input name="driverData.driving_license_number"
                                                 label="Nr prawa jazdy"
                                                 required="true"
                                                 wire:model="driverData.driving_license_number"/>
                    </div>
                    <div>
                        <x-form.input.date-picker name="driverData.driving_license_expiry_date"
                                                  label="Ważność prawa jazdy"
                                                  required="true"
                                                  wire:model="driverData.driving_license_expiry_date"
                                                  defaultDate="{{ $driverData['driving_license_expiry_date'] ?? '' }}"/>
                    </div>

                    <!-- Przód dokumentu -->
                    <x-form.document-upload
                        field="driving_license_document_front"
                        label="Dokument przód"
                        :file="$driverData['driving_license_document_front'] ?? null"
                        :existing-media-id="$this->existingMedia['driving_license_document_front'] ?? null" />

                    <!-- Tył dokumentu -->
                    <x-form.document-upload
                        field="driving_license_document_back"
                        label="Dokument tył"
                        :file="$driverData['driving_license_document_back'] ?? null"
                        :existing-media-id="$this->existingMedia['driving_license_document_back'] ?? null" />
                </div>
            </div>

            <!-- SEKCJA DOWÓD OSOBISTY -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950 mb-6">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Dowód osobisty</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <x-form.input.text-input name="driverData.identity_card_number"
                                                 label="Nr dowodu osobistego"
                                                 required="true"
                                                 wire:model="driverData.identity_card_number"/>
                    </div>
                    <div>
                        <x-form.input.date-picker name="driverData.identity_card_expiry_date"
                                                  label="Ważność dowodu osobistego"
                                                  required="true"
                                                  wire:model="driverData.identity_card_expiry_date"
                                                  defaultDate="{{ $driverData['identity_card_expiry_date'] ?? '' }}"/>
                    </div>

                    <!-- Przód dokumentu -->
                    <x-form.document-upload
                        field="identity_card_document_front"
                        label="Dokument przód"
                        :file="$driverData['identity_card_document_front'] ?? null"
                        :existing-media-id="$this->existingMedia['identity_card_document_front'] ?? null" />

                    <!-- Tył dokumentu -->
                    <x-form.document-upload
                        field="identity_card_document_back"
                        label="Dokument tył"
                        :file="$driverData['identity_card_document_back'] ?? null"
                        :existing-media-id="$this->existingMedia['identity_card_document_back'] ?? null" />
                </div>
            </div>

            <!-- Sekcja: Adres -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                        Adres Zamieszkania
                    </h2>
                </div>

                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">

                    <x-form.input.select name="driverData.country" label="Kraj" wire:model="driverData.country"
                                         :options="\App\Enums\CountriesEnum::getOptions()"
                                         default="{{ \App\Enums\CountriesEnum::POLAND }}"/>

                    <x-form.input.text-input name="driverData.zipcode" label="Kod pocztowy"
                                             wire:model="driverData.zipcode"/>

                    <x-form.input.text-input name="driverData.city" label="Miasto" wire:model="driverData.city"/>
                    <x-form.input.text-input name="driverData.street" label="Ulica" wire:model="driverData.street"/>
                    <x-form.input.text-input name="driverData.street_nr" label="Nr budynku"
                                             wire:model="driverData.street_nr"/>
                    <x-form.input.text-input name="driverData.home_nr" label="Nr lokalu"
                                             wire:model="driverData.home_nr"/>

                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                        Uwagi
                    </h2>
                </div>

                <x-form.input.text-input type="textarea" name="driverData.extra_info"
                                         wire:model="driverData.extra_info"/>
            </div>

        </div>

        <div class="flex items-center justify-end w-full gap-3 mt-6">
            <x-ui.button @click="open = false" class="w-full" size="sm" variant="outline">Close</x-ui.button>
            <x-ui.button type="submit" class="w-full" size="sm" variant="primary">Save Changes</x-ui.button>
        </div>

    </form>
{{--</div>--}}
