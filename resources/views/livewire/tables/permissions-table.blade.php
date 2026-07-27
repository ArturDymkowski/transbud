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

                <th scope="col"
                    class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                    {{ __('permissions.name') }}
                </th>

                <x-tables.th-sort
                    field="roles_count"
                    :label="__('permissions.roles_count')"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <th scope="col"
                    class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                    {{ __('labels.tables.actions') }}
                </th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($permissions as $permission)
                <tr wire:key="permission-row-{{ $permission->id }}">
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <x-form.input.checkbox name="check_{{ $permission->id }}" value="{{ $permission->id }}" x-model="selected" wire:key="checkbox-{{ $permission->id }}"/>
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $permission->id }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $permission->name }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $permission->roles_count }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500 dark:text-gray-400 flex space-x-2">
                            @can('permissions.edit')
                                <x-ui.tooltip :text="__('labels.tables.edit')">
                                    <a href="{{ route('permissions.edit', $permission->id) }}" wire:navigate>
                                        <x-heroicon-o-pencil-square class="w-6 h-6 hover:text-green-500"/>
                                    </a>
                                </x-ui.tooltip>
                            @endcan
                            @can('permissions.delete')
                                <x-ui.tooltip :text="__('labels.tables.delete')">
                                    <button type="button"
                                            wire:click="deletePermission({{ $permission->id }})"
                                            wire:confirm="{{ __('permissions.confirm_delete_permission') }}"
                                    >
                                        <x-heroicon-o-trash class="w-6 h-6 hover:text-red-500"/>
                                    </button>
                                </x-ui.tooltip>
                            @endcan
                        </div>
                    </td>
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
