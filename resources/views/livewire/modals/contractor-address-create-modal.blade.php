{{-- Modal: Nowy adres kontrahenta. Włączany przez @include w contractor-addresses-table.blade.php, korzysta ze stanu rodzica (ContractorAddressesTable). --}}
<x-ui.modal wire:model="showCreateModal" class="max-w-lg p-6 lg:p-8">
    <h4 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
        {{ __('address_book.create_title') }}
    </h4>

    <form wire:submit="createAddress">
        <x-form.errors-summary/>

        <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
            <x-form.input.select name="createAddressData.country" label="{{ __('labels.address.country') }}"
                                 wire:model="createAddressData.country"
                                 required="true"
                                 :options="\App\Enums\CountriesEnum::getOptions()"/>

            <x-form.input.text-input name="createAddressData.zipcode" label="{{ __('labels.address.zipcode') }}"
                                     required="true"
                                     wire:model="createAddressData.zipcode"/>

            <x-form.input.text-input name="createAddressData.city" label="{{ __('labels.address.city') }}"
                                     required="true"
                                     wire:model="createAddressData.city"/>
            <x-form.input.text-input name="createAddressData.street" label="{{ __('labels.address.street') }}"
                                     required="true"
                                     wire:model="createAddressData.street"/>
            <x-form.input.text-input name="createAddressData.house_nr" label="{{ __('labels.address.house_nr') }}"
                                     wire:model="createAddressData.house_nr"/>
            <x-form.input.text-input name="createAddressData.apartment_nr" label="{{ __('labels.address.apartment_nr') }}"
                                     wire:model="createAddressData.apartment_nr"/>
        </div>

        <x-form.actions/>
    </form>
</x-ui.modal>
