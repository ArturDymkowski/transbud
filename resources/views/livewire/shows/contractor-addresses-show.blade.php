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
            <x-form.address-fields prefix="addressData" :data="$addressData" disabled/>
        </x-form.section>

    </div>

    <x-tables.show-footer-actions :indexRoute="route('contractor-addresses.index')" :editRoute="route('contractor-addresses.edit', $contractorAddress)"/>

</div>
