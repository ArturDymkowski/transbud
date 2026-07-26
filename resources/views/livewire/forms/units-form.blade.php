<!-- Formularz -->
<form wire:submit="save">

    <x-form.errors-summary/>

    <div class="grid grid-cols-1 gap-6">

        <!-- Sekcja: Informacje podstawowe -->
        <x-form.section title="{{ __('units.basic_info') }}">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-3">

                <div class="col-span-1">
                    <x-form.input.text-input name="unitData.name"
                                             label="{{ __('units.name') }}"
                                             required="true"
                                             wire:model="unitData.name"
                    />
                </div>

            </div>
        </x-form.section>

    </div>

    <x-form.actions :cancelRoute="route('units.index')"/>

</form>
