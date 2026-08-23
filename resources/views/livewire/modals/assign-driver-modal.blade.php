{{-- Modal: Przypisanie kierowcy do pojazdu. Włączany przez @include w drivers-table.blade.php, korzysta ze stanu rodzica (DriversTable). --}}
<x-ui.modal wire:model="showAssignModal" class="max-w-md p-6 lg:p-8">
    <h4 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
        {{ __('vehicles.assign_driver') }}
    </h4>

    <form wire:submit="assignDriver">
        <x-form.errors-summary/>

        <x-form.input.searchable-select
            name="selectedDriverId"
            label="{{ __('drivers.singular_model_label') }}"
            wire:model="selectedDriverId"
            :options="$this->assignableDriverOptions"
            required="true"
        />

        <x-form.actions/>
    </form>
</x-ui.modal>
