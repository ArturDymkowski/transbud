<div class="space-y-6">

    <!-- Karta: Podsumowanie konta -->
    <div class="p-5 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
        <div class="flex flex-col items-center w-full gap-6 xl:flex-row">
            <div class="w-20 h-20 overflow-hidden border border-gray-200 rounded-full dark:border-gray-800">
                <img src="/images/user/owner.jpg" alt="{{ $user->name }}" />
            </div>

            <div class="text-center xl:text-left">
                <h4 class="mb-1 text-lg font-semibold text-gray-800 dark:text-white/90">
                    {{ $user->name }}
                </h4>
                <div class="flex flex-col items-center gap-1 xl:flex-row xl:gap-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $this->roleNames }}</p>
                    <div class="hidden h-3.5 w-px bg-gray-300 dark:bg-gray-700 xl:block"></div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Karta: Informacje osobiste -->
    <div class="p-5 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
        <div class="flex flex-col justify-between gap-5 lg:flex-row lg:items-center">
            <div class="w-full">
                <h4 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">
                    {{ __('profile.personal_info') }}
                </h4>

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-x-8">
                    <div>
                        <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">{{ __('users.name') }}</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">{{ __('users.email') }}</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $user->email }}</p>
                    </div>
                </div>
            </div>

            <x-ui.button wire:click="openInfoModal" size="sm" variant="outline" class="w-full shrink-0 lg:w-auto">
                <x-heroicon-o-pencil-square class="w-4 h-4" />
                {{ __('profile.edit') }}
            </x-ui.button>
        </div>
    </div>

    <!-- Karta: Hasło -->
    <div class="p-5 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
        <div class="flex flex-col justify-between gap-5 lg:flex-row lg:items-center">
            <div>
                <h4 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">
                    {{ __('profile.password') }}
                </h4>
                <p class="text-sm font-medium tracking-widest text-gray-800 dark:text-white/90">••••••••••</p>
            </div>

            <x-ui.button wire:click="openPasswordModal" size="sm" variant="outline" class="w-full shrink-0 lg:w-auto">
                <x-heroicon-o-lock-closed class="w-4 h-4" />
                {{ __('profile.change_password') }}
            </x-ui.button>
        </div>
    </div>

    <!-- Modal: Edycja informacji osobistych -->
    <x-ui.modal wire:model="showInfoModal" class="max-w-[600px] p-6 lg:p-8">
        <h4 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
            {{ __('profile.edit_personal_info') }}
        </h4>

        <form wire:submit="saveInfo">
            <x-form.errors-summary/>

            <div class="grid grid-cols-1 gap-5">
                <x-form.input.text-input name="profileData.name"
                                         label="{{ __('users.name') }}"
                                         required="true"
                                         wire:model="profileData.name"
                />

                <x-form.input.text-input name="profileData.email"
                                         label="{{ __('users.email') }}"
                                         required="true"
                                         wire:model="profileData.email"
                />
            </div>

            <x-form.actions/>
        </form>
    </x-ui.modal>

    <!-- Modal: Zmiana hasła -->
    <x-ui.modal wire:model="showPasswordModal" class="max-w-[500px] p-6 lg:p-8">
        <h4 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
            {{ __('profile.change_password') }}
        </h4>

        <form wire:submit="savePassword">
            <x-form.errors-summary/>

            <div class="grid grid-cols-1 gap-5">
                <x-form.input.text-input type="password" name="passwordData.current_password"
                                         label="{{ __('profile.current_password') }}"
                                         required="true"
                                         wire:model="passwordData.current_password"
                />

                <x-form.input.text-input type="password" name="passwordData.password"
                                         label="{{ __('profile.new_password') }}"
                                         required="true"
                                         wire:model="passwordData.password"
                />

                <x-form.input.text-input type="password" name="passwordData.password_confirmation"
                                         label="{{ __('users.password_confirmation') }}"
                                         wire:model="passwordData.password_confirmation"
                />
            </div>

            <x-form.actions/>
        </form>
    </x-ui.modal>

</div>
