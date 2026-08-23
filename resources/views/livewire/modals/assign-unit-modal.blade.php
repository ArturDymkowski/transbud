{{-- Modal: Przypisanie jednostki do towaru. Włączany przez @include w units-table.blade.php, korzysta ze stanu rodzica (UnitsTable). --}}
<x-ui.modal wire:model="showAssignModal" class="max-w-md p-6 lg:p-8">
    <h4 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
        {{ __('goods.assign_unit') }}
    </h4>

    <form wire:submit="assignUnit">
        <x-form.errors-summary/>

        <x-form.input.searchable-select
            name="selectedUnitId"
            label="{{ __('units.singular_model_label') }}"
            wire:model="selectedUnitId"
            :options="$this->assignableUnitOptions"
            required="true"
        />

        <x-form.actions/>
    </form>
</x-ui.modal>
