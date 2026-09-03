<x-tables.card :createRoute="route('contractors.create')">
    <x-slot:header>
        <x-tables.filter-bar searchModel="search">
            <!-- Trashed & Active -->
            <x-tables.filter-trashed-active :trashedOptions="$this->trashedOptions" name="active"/>
        </x-tables.filter-bar>
    </x-slot:header>

    <div class="max-w-full px-5 overflow-x-auto" x-data="tableSelection(@entangle('selected'), @entangle('idsOnPage'), {{ json_encode($contractors->pluck('id')) }})">
        @can('contractors.delete')
            <x-tables.selection-bar deleteAction="deleteSelected" :confirmMessage="__('labels.tables.confirm_delete_selected')"/>
        @endcan
        <x-tables.filter-badges :filters="$this->activeFilters"/>

        <table class="min-w-full">
            <thead>
            <tr class="border-gray-200 border-y dark:border-gray-700">
                @can('contractors.delete')
                    <x-tables.th>
                        <x-form.input.checkbox
                            name="selectAll"
                            @click="togglePage"
                            x-bind:checked="isAllPageSelected()"
                        />
                    </x-tables.th>
                @endcan

                <x-tables.th-sort
                    field="id"
                    label="ID"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <x-tables.th-sort
                    field="name"
                    :label="__('contractors.name')"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <x-tables.th>{{ __('contractors.email') }}</x-tables.th>

                <x-tables.th>{{ __('contractors.phone') }}</x-tables.th>

                <x-tables.th>{{ __('contractors.nip') }}</x-tables.th>

                <x-tables.th>{{ __('contractors.regon') }}</x-tables.th>

                <x-tables.th-sort
                    field="active"
                    :label="__('labels.tables.active')"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <x-tables.th>{{ __('labels.tables.actions') }}</x-tables.th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($contractors as $contractor)
                <tr wire:key="contractor-row-{{ $contractor->id }}">
                    @can('contractors.delete')
                        <x-tables.td>
                            <x-form.input.checkbox name="check_{{ $contractor->id }}" value="{{ $contractor->id }}" x-model="selected" wire:key="checkbox-{{ $contractor->id }}"/>
                        </x-tables.td>
                    @endcan
                    <x-tables.td>{{ $contractor->id }}</x-tables.td>
                    <x-tables.td>{{ $contractor->name ?? '-' }}</x-tables.td>
                    <x-tables.td>{{ $contractor->email ?? '-' }}</x-tables.td>
                    <x-tables.td>{{ $contractor->phone ?? '-' }}</x-tables.td>
                    <x-tables.td>{{ $contractor->nip ?? '-' }}</x-tables.td>
                    <x-tables.td>{{ $contractor->regon ?? '-' }}</x-tables.td>
                    <x-tables.td>
                        @if($contractor->trashed())
                            <span class="text-gray-400">-</span>
                        @else
                            <x-form.input.toggle wire:change="toggleActive({{ $contractor->id }})"
                                                 name="{{ $contractor->id }}" :isActive="$contractor->active" wire:key="toggle-{{ $contractor->id }}"
                                                 :disabled="! auth()->user()?->can('contractors.edit')"/>
                        @endif
                    </x-tables.td>
                    <x-tables.td class="flex space-x-2">
                        <x-tables.action-show :route="route('contractors.show', $contractor->id)"/>
                        @if($contractor->trashed())
                            @can('contractors.edit')
                                <x-tables.action-restore
                                    wire:click="restoreContractor({{ $contractor->id }})"
                                    :confirm="__('labels.tables.confirm_restore')"
                                />
                            @endcan
                            @can('contractors.delete')
                                <x-tables.action-delete
                                    wire:click="forceDeleteContractor({{ $contractor->id }})"
                                    :confirm="__('labels.tables.confirm_force_delete')"
                                    :label="__('labels.tables.force_delete')"
                                >
                                    <x-heroicon-o-x-mark class="w-6 h-6 hover:text-red-500"/>
                                </x-tables.action-delete>
                            @endcan
                        @else
                            @can('contractors.edit')
                                <x-tables.action-edit :route="route('contractors.edit', $contractor->id)"/>
                            @endcan
                            @can('contractors.delete')
                                <x-tables.action-delete
                                    wire:click="deleteContractor({{ $contractor->id }})"
                                    :confirm="__('contractors.confirm_delete_contractor')"
                                />
                            @endcan
                        @endif
                    </x-tables.td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <x-slot:footer>
        <x-tables.pagination-footer :paginator="$contractors"/>
    </x-slot:footer>
</x-tables.card>
