{{-- Modal: Dodawanie/edycja kosztu dostawy. Samodzielny komponent Livewire (App\Livewire\Modals\DeliveryCostModal),
     osadzony w delivery-profitability-panel.blade.php przez <livewire:modals.delivery-cost-modal>.
     Panel otwiera go zdarzeniami open-create-cost-modal/open-edit-cost-modal, a po zapisie modal
     odsyła zdarzenie cost-saved, żeby panel odświeżył listę kosztów i rentowność. --}}
<x-ui.modal wire:model="isOpen" class="max-w-lg p-6 lg:p-8">
    <h4 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
        {{ $editingCostId ? __('deliveries.cost.edit') : __('deliveries.cost.add') }}
    </h4>

    <form wire:submit="saveCost">
        <x-form.errors-summary/>

        <div class="grid grid-cols-1 gap-x-6 gap-y-5">
            <x-form.input.select name="costData.type"
                                 label="{{ __('deliveries.cost.type') }}"
                                 wire:model="costData.type"
                                 :options="$this->costTypeOptions"
                                 required="true"/>

            <div x-data="quantityInput(@entangle('costData.amount'))">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    {{ __('deliveries.cost.amount') }} ({{ $delivery->currency->label() }}) <x-form.input.required-star/>
                </label>
                <input type="text"
                       inputmode="decimal"
                       name="costData.amount"
                       :value="display"
                       x-on:input="onInput($event)"
                       @class([
                           'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30',
                           'border-gray-300 dark:border-gray-700' => !$errors->has('costData.amount'),
                           'border-red-300 dark:border-red-700' => $errors->has('costData.amount'),
                       ])/>
                @error('costData.amount')
                    <div class="mt-1 text-xs text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <x-form.input.select name="costData.delivery_transport_set_id"
                                 label="{{ __('deliveries.transport_set.transport_set') }}"
                                 wire:model="costData.delivery_transport_set_id"
                                 :options="$this->transportSetOptions"/>

            <x-form.input.text-input name="costData.description"
                                     label="{{ __('deliveries.cost.description') }}"
                                     wire:model="costData.description"/>
        </div>

        <x-form.actions/>
    </form>
</x-ui.modal>
