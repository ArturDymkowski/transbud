@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Kierowcy" />

    <!-- Formularz -->
    <form action="{{ route('drivers.update', $driver->id) }}" method="POST">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 bg-red-100 rounded-lg dark:bg-red-900 dark:text-red-200">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h4 class="mb-6 text-lg font-medium text-gray-800 dark:text-white/90">Edycja Kierowcy</h4>

        <div class="grid grid-cols-1 gap-6">

            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                        Dane podstawowe
                    </h2>
                </div>

                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                    <div class="col-span-1">
                        <x-form.input.text-input name="name"
                                                 label="Imię i Nazwisko"
                                                 required="true"
                                                 value="{{ old('name', $driver->name) }}" />
                    </div>

                    <div class="col-span-1">
                        <x-form.input.text-input name="phone"
                                                 label="Telefon"
                                                 required="true"
                                                 value="{{ old('phone', $driver->phone) }}" />
                    </div>

                    <div class="col-span-1">
                        <x-form.input.text-input name="pesel"
                                                 label="PESEL"
                                                 required="true"
                                                 value="{{ old('pesel', $driver->pesel) }}" />
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                        Uprawnienia i status
                    </h2>
                </div>

                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                    <x-form.input.text-input name="driving_license_number"
                                             label="Nr prawa jazdy"
                                             required="true"
                                             value="{{ old('driving_license_number', $driver->driving_license_number) }}"/>

                    <x-form.date-picker name="driving_license_expiry_date"
                                        label="Ważność prawa jazdy"
                                        required="true"
                                        defaultDate="{{ old('driving_license_expiry_date', $driver->driving_license_expiry_date) }}"/>

                    <x-form.date-picker name="medical_exam_expiry_date"
                                        label="Badania lekarskie do"
                                        defaultDate="{{ old('medical_exam_expiry_date', $driver->medical_exam_expiry_date) }}"/>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                        Adres Zamieszkania
                    </h2>
                </div>

                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                    <x-form.input.select name="country"
                                         label="Kraj"
                                         :options="\App\Enums\CountriesEnum::getOptions()"
                                         default="{{ old('country', $driver->country ?? \App\Enums\CountriesEnum::POLAND) }}"/>

                    <x-form.input.text-input name="zipcode"
                                             label="Kod pocztowy"
                                             value="{{ old('zipcode', $driver->zipcode) }}"/>

                    <x-form.input.text-input name="city"
                                             label="Miasto"
                                             value="{{ old('city', $driver->city) }}"/>

                    <x-form.input.text-input name="street"
                                             label="Ulica"
                                             value="{{ old('street', $driver->street) }}"/>

                    <x-form.input.text-input name="street_nr"
                                             label="Nr budynku"
                                             value="{{ old('street_nr', $driver->street_nr) }}"/>

                    <x-form.input.text-input name="home_nr"
                                             label="Nr lokalu"
                                             value="{{ old('home_nr', $driver->home_nr) }}"/>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                        Uwagi
                    </h2>
                </div>

                <x-form.input.text-input type="textarea"
                                         name="extra_info"
                                         value="{{ old('extra_info', $driver->extra_info) }}"/>
            </div>

        </div>

        <div class="flex items-center justify-end w-full gap-3 mt-6">
            <a href="{{ route('drivers.index') }}" class="w-full sm:w-auto px-4 py-2 text-sm text-center border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-800 dark:text-white dark:hover:bg-gray-900">
                Anuluj
            </a>
            <x-ui.button type="submit" class="w-full sm:w-auto" size="sm" variant="primary">
                Save Changes
            </x-ui.button>
        </div>

    </form>
@endsection
