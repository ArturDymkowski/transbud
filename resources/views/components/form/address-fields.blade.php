@props(['prefix', 'data' => [], 'disabled' => false, 'required' => false])

<div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
    @if($disabled)
        <x-form.input.select name="{{ $prefix }}.country" label="{{ __('labels.address.country') }}"
                             :default="$data['country']?->value ?? ''"
                             :options="\App\Enums\CountriesEnum::getOptions()"
                             disabled/>

        <x-form.input.text-input name="{{ $prefix }}.zipcode" label="{{ __('labels.address.zipcode') }}"
                                 :value="$data['zipcode'] ?? ''" disabled/>

        <x-form.input.text-input name="{{ $prefix }}.city" label="{{ __('labels.address.city') }}"
                                 :value="$data['city'] ?? ''" disabled/>
        <x-form.input.text-input name="{{ $prefix }}.street" label="{{ __('labels.address.street') }}"
                                 :value="$data['street'] ?? ''" disabled/>
        <x-form.input.text-input name="{{ $prefix }}.house_nr" label="{{ __('labels.address.house_nr') }}"
                                 :value="$data['house_nr'] ?? ''" disabled/>
        <x-form.input.text-input name="{{ $prefix }}.apartment_nr" label="{{ __('labels.address.apartment_nr') }}"
                                 :value="$data['apartment_nr'] ?? ''" disabled/>
    @else
        <x-form.input.select name="{{ $prefix }}.country" label="{{ __('labels.address.country') }}"
                             wire:model="{{ $prefix }}.country"
                             :required="$required"
                             :options="\App\Enums\CountriesEnum::getOptions()"/>

        <x-form.input.text-input name="{{ $prefix }}.zipcode" label="{{ __('labels.address.zipcode') }}"
                                 :required="$required"
                                 wire:model="{{ $prefix }}.zipcode"/>

        <x-form.input.text-input name="{{ $prefix }}.city" label="{{ __('labels.address.city') }}"
                                 :required="$required"
                                 wire:model="{{ $prefix }}.city"/>
        <x-form.input.text-input name="{{ $prefix }}.street" label="{{ __('labels.address.street') }}"
                                 :required="$required"
                                 wire:model="{{ $prefix }}.street"/>
        <x-form.input.text-input name="{{ $prefix }}.house_nr" label="{{ __('labels.address.house_nr') }}"
                                 wire:model="{{ $prefix }}.house_nr"/>
        <x-form.input.text-input name="{{ $prefix }}.apartment_nr" label="{{ __('labels.address.apartment_nr') }}"
                                 wire:model="{{ $prefix }}.apartment_nr"/>
    @endif
</div>
