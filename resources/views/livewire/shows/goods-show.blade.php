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

    <div class="flex items-center justify-end w-full gap-3 mt-6">
        <x-ui.button class="w-full" size="sm" variant="outline">
            <a href="{{ route('goods.index') }}" wire:navigate>{{ __('labels.general.close') }}</a>
        </x-ui.button>
        <x-ui.button class="w-full" size="sm" variant="primary">
            <a href="{{ route('goods.edit', $good) }}" wire:navigate>{{ __('labels.tables.edit') }}</a>
        </x-ui.button>
    </div>

</div>
