<!-- Formularz -->
<form wire:submit="save">

    <x-form.errors-summary/>

    <div class="grid grid-cols-1 gap-6">

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
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">

                <x-form.input.select name="addressData.country" label="{{ __('labels.address.country') }}" wire:model="addressData.country"
                                     required="true"
                                     :options="\App\Enums\CountriesEnum::getOptions()"/>

                <x-form.input.text-input name="addressData.zipcode" label="{{ __('labels.address.zipcode') }}"
                                         required="true"
                                         wire:model="addressData.zipcode"/>

                <x-form.input.text-input name="addressData.city" label="{{ __('labels.address.city') }}"
                                         required="true"
                                         wire:model="addressData.city"/>
                <x-form.input.text-input name="addressData.street" label="{{ __('labels.address.street') }}"
                                         required="true"
                                         wire:model="addressData.street"/>
                <x-form.input.text-input name="addressData.house_nr" label="{{ __('labels.address.house_nr') }}"
                                         wire:model="addressData.house_nr"/>
                <x-form.input.text-input name="addressData.apartment_nr" label="{{ __('labels.address.apartment_nr') }}"
                                         wire:model="addressData.apartment_nr"/>

            </div>
        </x-form.section>

    </div>

    <x-form.actions :cancelRoute="route('contractor-addresses.index')"/>

</form>
