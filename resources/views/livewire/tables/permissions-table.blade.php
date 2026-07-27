<x-tables.card>
    <x-slot:header>
        <x-tables.filter-bar searchModel="search"/>
    </x-slot:header>

    <div class="max-w-full px-5 overflow-x-auto" x-data="tableSelection(@entangle('selected'), @entangle('idsOnPage'), {{ json_encode($permissions->pluck('id')) }})">
        <x-tables.selection-bar deleteAction="deleteSelected" :confirmMessage="__('labels.tables.confirm_delete_selected')"/>
        <x-tables.filter-badges :filters="$this->activeFilters"/>

        <table class="min-w-full">
            <thead>
            <tr class="border-gray-200 border-y dark:border-gray-700">
                <x-tables.th>
                    <x-form.input.checkbox
                        name="selectAll"
                        @click="togglePage"
                        x-bind:checked="isAllPageSelected()"
                    />
                </x-tables.th>

                <x-tables.th-sort
                    field="id"
                    label="ID"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <x-tables.th>{{ __('permissions.name') }}</x-tables.th>

                <x-tables.th-sort
                    field="roles_count"
                    :label="__('permissions.roles_count')"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <x-tables.th>{{ __('labels.tables.actions') }}</x-tables.th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($permissions as $permission)
                <tr wire:key="permission-row-{{ $permission->id }}">
                    <x-tables.td>
                        <x-form.input.checkbox name="check_{{ $permission->id }}" value="{{ $permission->id }}" x-model="selected" wire:key="checkbox-{{ $permission->id }}"/>
                    </x-tables.td>
                    <x-tables.td>{{ $permission->id }}</x-tables.td>
                    <x-tables.td>{{ $permission->name }}</x-tables.td>
                    <x-tables.td>{{ $permission->roles_count }}</x-tables.td>
                    <x-tables.td class="flex space-x-2">
                        @can('permissions.edit')
                            <x-tables.action-edit :route="route('permissions.edit', $permission->id)"/>
                        @endcan
                        @can('permissions.delete')
                            <x-tables.action-delete
                                wire:click="deletePermission({{ $permission->id }})"
                                :confirm="__('permissions.confirm_delete_permission')"
                            />
                        @endcan
                    </x-tables.td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        {{ __('labels.tables.no_results') }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <x-slot:footer>
        <x-tables.pagination-footer :paginator="$permissions"/>
    </x-slot:footer>
</x-tables.card>
