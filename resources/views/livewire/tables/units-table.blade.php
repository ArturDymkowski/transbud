<div>
    @if($good && ! $readonly)
        <div class="flex w-full justify-end mb-4">
            <x-ui.button wire:click="openAssignModal" size="sm" variant="primary">
                {{ __('goods.assign_unit') }}
            </x-ui.button>
        </div>

        @include('livewire.modals.assign-unit-modal')
    @endif

    <x-tables.card :createRoute="$good ? null : route('units.create')">
    <x-slot:header>
        <x-tables.filter-bar searchModel="search">
            <!-- Trashed & Active -->
            <x-tables.filter-trashed-active :trashedOptions="$this->trashedOptions"/>
        </x-tables.filter-bar>
    </x-slot:header>

    <div class="max-w-full px-5 overflow-x-auto" x-data="tableSelection(@entangle('selected'), @entangle('idsOnPage'), {{ json_encode($units->pluck('id')) }})">
        @unless($readonly)
            <x-tables.selection-bar deleteAction="deleteSelected" :confirmMessage="$good ? __('goods.confirm_remove_unit_assignments') : __('labels.tables.confirm_delete_selected')"/>
        @endunless
        <x-tables.filter-badges :filters="$this->activeFilters"/>

        <table class="min-w-full">
            <thead>
            <tr class="border-gray-200 border-y dark:border-gray-700">
                @unless($readonly)
                    <x-tables.th>
                        <x-form.input.checkbox
                            name="selectAll"
                            @click="togglePage"
                            x-bind:checked="isAllPageSelected()"
                        />
                    </x-tables.th>
                @endunless

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

                <x-tables.th>{{ __('labels.tables.actions') }}</x-tables.th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($units as $unit)
                <tr wire:key="unit-row-{{ $unit->id }}">
                    @unless($readonly)
                        <x-tables.td>
                            <x-form.input.checkbox name="check_{{ $unit->id }}" value="{{ $unit->id }}" x-model="selected" wire:key="checkbox-{{ $unit->id }}"/>
                        </x-tables.td>
                    @endunless
                    <x-tables.td>{{ $unit->id }}</x-tables.td>
                    <x-tables.td>{{ $unit->name }}</x-tables.td>
                    @unless($good)
                        <x-tables.td>
                            <x-form.input.toggle wire:change="toggleActive({{ $unit->id }})"
                                                 name="{{ $unit->id }}" :isActive="$unit->is_active" wire:key="toggle-{{ $unit->id }}"
                                                 :disabled="! auth()->user()?->can('units.edit')"/>
                        </x-tables.td>
                    @endunless
                    <x-tables.td class="flex space-x-2">
                        <x-tables.action-show :route="route('units.show', $unit->id)"/>
                        @unless($readonly)
                            @can('units.edit')
                                <x-tables.action-edit :route="route('units.edit', $unit->id)"/>
                            @endcan
                            @if($good)
                                @can('goods.edit')
                                    <x-tables.action-delete
                                        wire:click="deleteUnit({{ $unit->id }})"
                                        :confirm="__('goods.confirm_remove_unit_assignment')"
                                        :label="__('goods.remove_unit_assignment')"
                                    >
                                        <x-heroicon-o-link-slash class="w-6 h-6 hover:text-red-500"/>
                                    </x-tables.action-delete>
                                @endcan
                            @else
                                @can('units.delete')
                                    <x-tables.action-delete
                                        wire:click="deleteUnit({{ $unit->id }})"
                                        :confirm="__('units.confirm_delete_unit')"
                                    />
                                @endcan
                            @endif
                        @endunless
                    </x-tables.td>
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
