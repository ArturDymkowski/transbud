<form wire:submit="save">

    <x-form.errors-summary/>

    <div class="grid grid-cols-1 gap-6">
        <x-form.section title="{{ __('permissions.basic_info') }}">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-3">
                <div class="col-span-1">
                    <x-form.input.text-input name="permissionData.name"
                                             label="{{ __('permissions.name') }}"
                                             required="true"
                                             wire:model="permissionData.name"
                    />
                </div>
            </div>
        </x-form.section>
    </div>

    <x-form.actions :cancelRoute="route('permissions.index')"/>

</form>
