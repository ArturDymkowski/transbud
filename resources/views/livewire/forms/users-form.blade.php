<!-- Formularz -->
<form wire:submit="save">

    <x-form.errors-summary/>

    <div class="grid grid-cols-1 gap-6">

        <!-- Sekcja: Informacje podstawowe -->
        <x-form.section title="{{ __('users.basic_info') }}">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-3">

                <div class="col-span-1">
                    <x-form.input.text-input name="userData.name"
                                             label="{{ __('users.name') }}"
                                             required="true"
                                             wire:model="userData.name"
                    />
                </div>

                <div class="col-span-1">
                    <x-form.input.text-input name="userData.email"
                                             label="{{ __('users.email') }}"
                                             required="true"
                                             wire:model="userData.email"
                    />
                </div>

            </div>
        </x-form.section>

        <!-- Sekcja: Hasło -->
        <x-form.section title="{{ __('users.password_section') }}">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-3">

                <div class="col-span-1">
                    <x-form.input.text-input type="password" name="userData.password"
                                             label="{{ __('users.password') }}"
                                             :required="! $user->exists"
                                             placeholder="{{ $user->exists ? __('users.password_keep_hint') : '' }}"
                                             wire:model="userData.password"
                    />
                </div>

                <div class="col-span-1">
                    <x-form.input.text-input type="password" name="userData.password_confirmation"
                                             label="{{ __('users.password_confirmation') }}"
                                             wire:model="userData.password_confirmation"
                    />
                </div>

            </div>
        </x-form.section>

    </div>

    <x-form.actions :cancelRoute="route('users.index')"/>

</form>
