<x-tables.card :createRoute="auth()->user()->can('roles.create') ? route('roles.create') : null">
    <div class="max-w-full px-5 overflow-x-auto" x-data="tableSelection(@entangle('selected'), @entangle('idsOnPage'), {{ json_encode($roles->pluck('id')) }})">
        <x-tables.selection-bar deleteAction="deleteSelected" :confirmMessage="__('labels.tables.confirm_delete_selected')"/>

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
                <th scope="col"
                    class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                    ID
                </th>
                <th scope="col"
                    class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                    {{ __('roles.name') }}
                </th>
                <th scope="col"
                    class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                    {{ __('roles.permissions_count') }}
                </th>
                <th scope="col"
                    class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                    {{ __('labels.tables.actions') }}
                </th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($roles as $role)
                <tr wire:key="role-row-{{ $role->id }}">
                    <x-tables.td>
                        <x-form.input.checkbox name="check_{{ $role->id }}" value="{{ $role->id }}" x-model="selected" wire:key="checkbox-{{ $role->id }}"/>
                    </x-tables.td>
                    <x-tables.td>{{ $role->id }}</x-tables.td>
                    <x-tables.td>{{ $role->name }}</x-tables.td>
                    <x-tables.td>{{ $role->permissions_count }}</x-tables.td>
                    <x-tables.td class="flex space-x-2">
                        @can('roles.edit')
                            <x-ui.tooltip :text="__('labels.tables.edit')">
                                <a href="{{ route('roles.edit', $role->id) }}" wire:navigate>
                                    <x-heroicon-o-pencil-square class="w-6 h-6 hover:text-green-500"/>
                                </a>
                            </x-ui.tooltip>
                        @endcan
                        @can('roles.delete')
                            <x-ui.tooltip :text="__('labels.tables.delete')">
                                <button type="button"
                                        wire:click="deleteRole({{ $role->id }})"
                                        wire:confirm="{{ __('roles.confirm_delete_role') }}"
                                >
                                    <x-heroicon-o-trash class="w-6 h-6 hover:text-red-500"/>
                                </button>
                            </x-ui.tooltip>
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
</x-tables.card>
