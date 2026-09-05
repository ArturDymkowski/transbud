<!-- Podgląd -->
<div>

    <div class="grid grid-cols-1 gap-6">

        <!-- Sekcja: Informacje podstawowe -->
        <x-form.section title="{{ __('goods.basic_info') }}">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-3">

                <div class="col-span-1">
                    <x-form.input.text-input name="goodData.name"
                                             label="{{ __('goods.name') }}"
                                             :value="$goodData['name'] ?? ''"
                                             disabled/>
                </div>

                <div class="col-span-1">
                    <x-form.input.select name="goodData.default_unit_id"
                                         label="{{ __('goods.default_unit') }}"
                                         :default="$goodData['default_unit_id'] ?? ''"
                                         :options="$this->unitOptions"
                                         disabled/>
                </div>

            </div>
        </x-form.section>

        <!-- Sekcja: Informacje dodatkowe -->
        <x-form.section title="{{ __('goods.description') }}">
            <x-form.input.text-input type="textarea" name="goodData.description"
                                     :value="$goodData['description'] ?? ''" disabled/>
        </x-form.section>

    </div>

    <x-tables.show-footer-actions :indexRoute="route('goods.index')" :editRoute="auth()->user()->can('goods.edit') ? route('goods.edit', $good) : null"/>

</div>
