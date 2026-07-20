<div>
    <div class="flex w-full justify-end mb-4">
        <x-ui.button wire:click="openAssignModal" size="sm" variant="primary">
            {{ __('vehicles.assign_driver') }}
        </x-ui.button>
    </div>

    <x-ui.modal wire:model="showAssignModal" class="max-w-md p-6 lg:p-8">
        <h4 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
            {{ __('vehicles.assign_driver') }}
        </h4>

        <form wire:submit="assignDriver">
            <x-form.errors-summary/>

            <x-form.input.select
                name="selectedDriverId"
                label="{{ __('drivers.singular_model_label') }}"
                wire:model="selectedDriverId"
                :options="$this->assignableDriverOptions"
                required="true"
            />

            <x-form.actions/>
        </form>
    </x-ui.modal>

<x-tables.card>
    <x-slot:header>
        <x-tables.filter-bar searchModel="search"/>
    </x-slot:header>

    <div class="max-w-full px-5 overflow-x-auto">
        <x-tables.filter-badges :filters="$this->activeFilters"/>

        <table class="min-w-full">
            <thead>
            <tr class="border-gray-200 border-y dark:border-gray-700">
                <th scope="col"
                    class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                    {{ __('vehicles.driver_name') }}
                </th>

                <th scope="col"
                    class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                    {{ __('vehicles.assigned_at') }}
                </th>

                <th scope="col"
                    class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                    {{ __('labels.tables.actions') }}
                </th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($drivers as $driver)
                <tr wire:key="assigned-driver-row-{{ $driver->id }}">
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $driver->name }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $driver->pivot->created_at?->format('Y-m-d') ?? '-' }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400 flex space-x-2">
                            <x-ui.tooltip :text="__('vehicles.remove_driver_assignment')">
                                <button type="button"
                                        wire:click="removeAssignment({{ $driver->id }})"
                                        wire:confirm="{{ __('vehicles.confirm_remove_driver_assignment') }}"
                                >
                                    <x-heroicon-o-link-slash class="w-6 h-6 hover:text-red-500"/>
                                </button>
                            </x-ui.tooltip>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <x-slot:footer>
        <x-tables.pagination-footer :paginator="$drivers"/>
    </x-slot:footer>
</x-tables.card>
</div>
