<!-- Formularz -->
<x-form.wrapper :cancelRoute="route('units.index')">

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

</x-form.wrapper>
