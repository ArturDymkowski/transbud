<!-- Podgląd -->
<div>

    <div class="grid grid-cols-1 gap-6">

        <!-- Sekcja: Kontrahent -->
        <x-form.section title="{{ __('address_book.contractor') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <x-form.input.select name="addressData.contractor_id"
                                     label="{{ __('address_book.contractor') }}"
                                     :default="$addressData['contractor_id'] ?? ''"
                                     :options="$this->contractorOptions"
                                     disabled/>
            </div>
        </x-form.section>

        <!-- Sekcja: Adres -->
        <x-form.section title="{{ __('labels.address.address') }}">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">

                <x-form.input.select name="addressData.country" label="{{ __('labels.address.country') }}"
                                     :default="$addressData['country']?->value ?? ''"
                                     :options="\App\Enums\CountriesEnum::getOptions()"
                                     disabled/>

                <x-form.input.text-input name="addressData.zipcode" label="{{ __('labels.address.zipcode') }}"
                                         :value="$addressData['zipcode'] ?? ''" disabled/>

                <x-form.input.text-input name="addressData.city" label="{{ __('labels.address.city') }}"
                                         :value="$addressData['city'] ?? ''" disabled/>
                <x-form.input.text-input name="addressData.street" label="{{ __('labels.address.street') }}"
                                         :value="$addressData['street'] ?? ''" disabled/>
                <x-form.input.text-input name="addressData.house_nr" label="{{ __('labels.address.house_nr') }}"
                                         :value="$addressData['house_nr'] ?? ''" disabled/>
                <x-form.input.text-input name="addressData.apartment_nr" label="{{ __('labels.address.apartment_nr') }}"
                                         :value="$addressData['apartment_nr'] ?? ''" disabled/>

            </div>
        </x-form.section>

    </div>

    <x-tables.show-footer-actions :indexRoute="route('contractor-addresses.index')" :editRoute="route('contractor-addresses.edit', $contractorAddress)"/>

</div>
