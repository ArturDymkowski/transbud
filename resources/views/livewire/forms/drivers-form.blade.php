{{--<div class="relative w-full max-h-[90vh] overflow-y-auto rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">--}}

    <!-- Formularz -->
    <form wire:submit="updateDriver">

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

                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">

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

            <!-- Sekcja: Dokumenty -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                        Uprawnienia i status
                    </h2>
                </div>

                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">

                    <x-form.input.text-input name="driverData.driving_license_number"
                                             label="Nr prawa jazdy"
                                             required="true"
                                             wire:model="driverData.driving_license_number"/>

                    <x-form.date-picker name="driverData.driving_license_expiry_date"
                                        label="Ważność prawa jazdy"
                                        required="true"
                                        wire:model="driverData.driving_license_expiry_date"
                                        defaultDate="{{ $driverData['driving_license_expiry_date'] ?? '' }}"/>

                    <x-form.date-picker name="driverData.medical_exam_expiry_date" label="Badania lekarskie do"
                                        wire:model="driverData.medical_exam_expiry_date"
                                        defaultDate="{{ $driverData['medical_exam_expiry_date'] ?? '' }}"/>

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
