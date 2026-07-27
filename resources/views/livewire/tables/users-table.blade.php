<x-tables.card :createRoute="route('users.create')">
    <x-slot:header>
        <x-tables.filter-bar searchModel="search">
            <!-- Trashed & Active -->
            <x-tables.filter-trashed-active :trashedOptions="$this->trashedOptions"/>
        </x-tables.filter-bar>
    </x-slot:header>

    <div class="max-w-full px-5 overflow-x-auto" x-data="tableSelection(@entangle('selected'), @entangle('idsOnPage'), {{ json_encode($users->pluck('id')) }})">
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

                <x-tables.th-sort
                    field="name"
                    :label="__('users.name')"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <x-tables.th-sort
                    field="email"
                    :label="__('users.email')"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <x-tables.th-sort
                    field="is_active"
                    :label="__('labels.tables.active')"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <x-tables.th>{{ __('labels.tables.actions') }}</x-tables.th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($users as $user)
                <tr wire:key="user-row-{{ $user->id }}">
                    <x-tables.td>
                        <x-form.input.checkbox name="check_{{ $user->id }}" value="{{ $user->id }}" x-model="selected" wire:key="checkbox-{{ $user->id }}"/>
                    </x-tables.td>
                    <x-tables.td>{{ $user->id }}</x-tables.td>
                    <x-tables.td>{{ $user->name }}</x-tables.td>
                    <x-tables.td>{{ $user->email }}</x-tables.td>
                    <x-tables.td>
                        <x-form.input.toggle wire:change="toggleActive({{ $user->id }})"
                                             name="{{ $user->id }}" :isActive="$user->is_active" wire:key="toggle-{{ $user->id }}"/>
                    </x-tables.td>
                    <x-tables.td class="flex space-x-2">
                        <x-tables.action-show :route="route('users.show', $user->id)"/>
                        <x-tables.action-edit :route="route('users.edit', $user->id)"/>
                        <x-tables.action-delete
                            wire:click="deleteUser({{ $user->id }})"
                            :confirm="__('users.confirm_delete_user')"
                        />
                    </x-tables.td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <x-slot:footer>
        <x-tables.pagination-footer :paginator="$users"/>
    </x-slot:footer>
</x-tables.card>
