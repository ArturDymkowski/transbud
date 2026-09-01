<!-- Formularz -->
<x-form.wrapper :cancelRoute="route('users.index')">

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

    <!-- Sekcja: Rola -->
    <x-form.section title="{{ __('users.role_section') }}">
        <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-3">

            <div class="col-span-1">
                <x-form.input.select name="userData.role_id"
                                     label="{{ __('users.role') }}"
                                     wire:model="userData.role_id"
                                     :options="$this->roleOptions"
                                     :disabled="$isEditingSelf"/>
                @if($isEditingSelf)
                    <p class="mt-1.5 text-xs text-gray-400">{{ __('users.cannot_change_own_role') }}</p>
                @endif
            </div>

        </div>
    </x-form.section>

</x-form.wrapper>
