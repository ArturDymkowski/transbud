<!-- Formularz -->
<form wire:submit="save">

    <x-form.errors-summary/>

    <div class="grid grid-cols-1 gap-6">

        <!-- Sekcja: Informacje podstawowe -->
        <x-form.section title="{{ __('goods.basic_info') }}">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-3">

                <div class="col-span-1">
                    <x-form.input.text-input name="goodData.name"
                                             label="{{ __('goods.name') }}"
                                             required="true"
                                             wire:model="goodData.name"
                    />
                </div>

                <div class="col-span-1">
                    <x-form.input.select name="goodData.default_unit_id"
                                         label="{{ __('goods.default_unit') }}"
                                         wire:model="goodData.default_unit_id"
                                         :options="$this->unitOptions"
                                         required="true"/>
                </div>

            </div>
        </x-form.section>

        <!-- Sekcja: Informacje dodatkowe -->
        <x-form.section title="{{ __('goods.description') }}">
            <x-form.input.text-input type="textarea" name="goodData.description"
                                     wire:model="goodData.description"/>
        </x-form.section>

    </div>

    <x-form.actions :cancelRoute="route('goods.index')"/>

</form>
