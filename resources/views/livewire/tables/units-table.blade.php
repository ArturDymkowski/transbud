<div>
    @if($good)
        <div class="flex w-full justify-end mb-4">
            <x-ui.button wire:click="openAssignModal" size="sm" variant="primary">
                {{ __('goods.assign_unit') }}
            </x-ui.button>
        </div>

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
    @endif

    <x-tables.card :createRoute="$good ? null : route('units.create')">
    <x-slot:header>
        <x-tables.filter-bar searchModel="search">
            <!-- Trashed -->
            <x-form.input.select :label="__('labels.tables.trashed')" :options="$this->trashedOptions" name="trashed" wire:model.live="trashed"/>

            <!-- Active -->
            <x-form.input.select :label="__('labels.tables.active')" :options="[
				         '' => __('labels.tables.all'),
                         0 => __('labels.tables.no'),
                         1 => __('labels.tables.yes')
			]" name="isActive" wire:model.live="isActive"/>
        </x-tables.filter-bar>
    </x-slot:header>

    <div class="max-w-full px-5 overflow-x-auto" x-data="tableSelection(@entangle('selected'), @entangle('idsOnPage'), {{ json_encode($units->pluck('id')) }})">
        <x-tables.selection-bar deleteAction="deleteSelected" :confirmMessage="$good ? __('goods.confirm_remove_unit_assignments') : __('labels.tables.confirm_delete_selected')"/>
        <x-tables.filter-badges :filters="$this->activeFilters"/>

        <table class="min-w-full">
            <thead>
            <tr class="border-gray-200 border-y dark:border-gray-700">
                <th scope="col"
                    class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                    <x-form.input.checkbox
                        name="selectAll"
                        @click="togglePage"
                        x-bind:checked="isAllPageSelected()"
                    />
                </th>

                <x-tables.th-sort
                    field="id"
                    label="ID"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <x-tables.th-sort
                    field="name"
                    :label="__('units.name')"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                @unless($good)
                    <x-tables.th-sort
                        field="is_active"
                        :label="__('labels.tables.active')"
                        :sortField="$sortField"
                        :sortDirection="$sortDirection"
                    />
                @endunless

                <th scope="col"
                    class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                    {{ __('labels.tables.actions') }}
                </th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($units as $unit)
                <tr wire:key="unit-row-{{ $unit->id }}">
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <x-form.input.checkbox name="check_{{ $unit->id }}" value="{{ $unit->id }}" x-model="selected" wire:key="checkbox-{{ $unit->id }}"/>
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $unit->id }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $unit->name }}</div>
                    </td>
                    @unless($good)
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <x-form.input.toggle wire:change="toggleActive({{ $unit->id }})"
                                                     name="{{ $unit->id }}" :isActive="$unit->is_active" wire:key="toggle-{{ $unit->id }}"/>
                            </div>
                        </td>
                    @endunless
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400 flex space-x-2">
                            <x-ui.tooltip :text="__('labels.tables.edit')">
                                <a href="{{ route('units.edit', $unit->id) }}" wire:navigate>
                                    <x-heroicon-o-pencil-square class="w-6 h-6 hover:text-green-500"/>
                                </a>
                            </x-ui.tooltip>
                            @if($good)
                                <x-ui.tooltip :text="__('goods.remove_unit_assignment')">
                                    <button type="button"
                                            wire:click="deleteUnit({{ $unit->id }})"
                                            wire:confirm="{{ __('goods.confirm_remove_unit_assignment') }}"
                                    >
                                        <x-heroicon-o-link-slash class="w-6 h-6 hover:text-red-500"/>
                                    </button>
                                </x-ui.tooltip>
                            @else
                                <x-ui.tooltip :text="__('labels.tables.delete')">
                                    <button type="button"
                                            wire:click="deleteUnit({{ $unit->id }})"
                                            wire:confirm="{{ __('units.confirm_delete_unit') }}"
                                    >
                                        <x-heroicon-o-trash class="w-6 h-6 hover:text-red-500"/>
                                    </button>
                                </x-ui.tooltip>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <x-slot:footer>
        <x-tables.pagination-footer :paginator="$units"/>
    </x-slot:footer>
</x-tables.card>
</div>
