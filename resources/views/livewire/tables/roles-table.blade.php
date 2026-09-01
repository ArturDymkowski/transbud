<x-tables.card :createRoute="auth()->user()->can('roles.create') ? route('roles.create') : null">
    <x-slot:header>
        <x-tables.filter-bar searchModel="search"/>
    </x-slot:header>

    <div class="max-w-full px-5 overflow-x-auto" x-data="tableSelection(@entangle('selected'), @entangle('idsOnPage'), {{ json_encode($roles->pluck('id')) }})">
        @can('roles.delete')
            <x-tables.selection-bar deleteAction="deleteSelected" :confirmMessage="__('labels.tables.confirm_delete_selected')"/>
        @endcan

        <table class="min-w-full">
            <thead>
            <tr class="border-gray-200 border-y dark:border-gray-700">
                @can('roles.delete')
                    <x-tables.th>
                        <x-form.input.checkbox
                            name="selectAll"
                            @click="togglePage"
                            x-bind:checked="isAllPageSelected()"
                        />
                    </x-tables.th>
                @endcan
                <x-tables.th>ID</x-tables.th>
                <x-tables.th>{{ __('roles.name') }}</x-tables.th>
                <x-tables.th>{{ __('roles.permissions_count') }}</x-tables.th>
                <x-tables.th>{{ __('labels.tables.actions') }}</x-tables.th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($roles as $role)
                <tr wire:key="role-row-{{ $role->id }}">
                    @can('roles.delete')
                        <x-tables.td>
                            <x-form.input.checkbox name="check_{{ $role->id }}" value="{{ $role->id }}" x-model="selected" wire:key="checkbox-{{ $role->id }}"/>
                        </x-tables.td>
                    @endcan
                    <x-tables.td>{{ $role->id }}</x-tables.td>
                    <x-tables.td>{{ $role->name }}</x-tables.td>
                    <x-tables.td>{{ $role->permissions_count }}</x-tables.td>
                    <x-tables.td class="flex space-x-2">
                        @can('roles.edit')
                            <x-tables.action-edit :route="route('roles.edit', $role->id)"/>
                        @endcan
                        @can('roles.delete')
                            <x-tables.action-delete
                                wire:click="deleteRole({{ $role->id }})"
                                :confirm="__('roles.confirm_delete_role')"
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
</x-tables.card>
