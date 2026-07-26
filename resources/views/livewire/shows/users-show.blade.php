<!-- Podgląd -->
<div>

    <div class="grid grid-cols-1 gap-6">

        <!-- Sekcja: Informacje podstawowe -->
        <x-form.section title="{{ __('users.basic_info') }}">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-3">

                <div class="col-span-1">
                    <x-form.input.text-input name="userData.name"
                                             label="{{ __('users.name') }}"
                                             :value="$userData['name'] ?? ''"
                                             disabled/>
                </div>

                <div class="col-span-1">
                    <x-form.input.text-input name="userData.email"
                                             label="{{ __('users.email') }}"
                                             :value="$userData['email'] ?? ''"
                                             disabled/>
                </div>

            </div>
        </x-form.section>

    </div>

    <div class="flex items-center justify-end w-full gap-3 mt-6">
        <x-ui.button class="w-full" size="sm" variant="outline">
            <a href="{{ route('users.index') }}" wire:navigate>{{ __('labels.general.close') }}</a>
        </x-ui.button>
        <x-ui.button class="w-full" size="sm" variant="primary">
            <a href="{{ route('users.edit', $user) }}" wire:navigate>{{ __('labels.tables.edit') }}</a>
        </x-ui.button>
    </div>

</div>
