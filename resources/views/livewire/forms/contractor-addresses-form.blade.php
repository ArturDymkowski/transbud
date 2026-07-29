<!-- Formularz -->
<x-form.wrapper :cancelRoute="route('contractor-addresses.index')">

    <!-- Sekcja: Kontrahent -->
    <x-form.section title="{{ __('address_book.contractor') }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <x-form.input.select name="addressData.contractor_id"
                                 label="{{ __('address_book.contractor') }}"
                                 wire:model="addressData.contractor_id"
                                 required="true"
                                 :options="$this->contractorOptions"/>
        </div>
    </x-form.section>

    <!-- Sekcja: Adres -->
    <x-form.section title="{{ __('labels.address.address') }}">
        <x-form.address-fields prefix="addressData" required/>
    </x-form.section>

</x-form.wrapper>
