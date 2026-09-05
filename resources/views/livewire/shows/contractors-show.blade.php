<!-- Podgląd -->
<div>

    <div class="grid grid-cols-1 gap-6">

        <!-- Sekcja: Informacje podstawowe -->
        <x-form.section title="{{ __('labels.general.basic_info') }}">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-3">

                <div class="col-span-1 sm:col-span-2">
                    <x-form.input.text-input name="contractorData.name"
                                             label="{{ __('contractors.name') }}"
                                             :value="$contractorData['name'] ?? ''"
                                             disabled/>
                </div>

                <x-form.input.text-input name="contractorData.nip"
                                         label="{{ __('contractors.nip') }}"
                                         :value="$contractorData['nip'] ?? ''" disabled/>

                <x-form.input.text-input name="contractorData.regon"
                                         label="{{ __('contractors.regon') }}"
                                         :value="$contractorData['regon'] ?? ''" disabled/>

            </div>
        </x-form.section>

        <!-- SEKCJA DANE KONTAKTOWE -->
        <x-form.section title="{{ __('contractors.contact_data') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <x-form.input.text-input name="contractorData.phone"
                                         label="{{ __('contractors.phone') }}"
                                         :value="$contractorData['phone'] ?? ''" disabled/>

                <x-form.input.text-input name="contractorData.email"
                                         label="{{ __('contractors.email') }}"
                                         :value="$contractorData['email'] ?? ''" disabled/>
            </div>
        </x-form.section>

        <!-- Sekcja: Informacje dodatkowe -->
        <x-form.section title="{{ __('labels.general.extra_info') }}">
            <x-form.input.text-input type="textarea" name="contractorData.notes"
                                     :value="$contractorData['notes'] ?? ''" disabled/>
        </x-form.section>

    </div>

    <x-tables.show-footer-actions :indexRoute="route('contractors.index')" :editRoute="auth()->user()->can('contractors.edit') ? route('contractors.edit', $contractor) : null"/>

</div>
