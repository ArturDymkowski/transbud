<x-tables.card :createRoute="route('goods.create')">
    <x-slot:header>
        <x-tables.filter-bar searchModel="search">
            <!-- Trashed & Active -->
            <x-tables.filter-trashed-active :trashedOptions="$this->trashedOptions"/>
        </x-tables.filter-bar>
    </x-slot:header>

    <div class="max-w-full px-5 overflow-x-auto" x-data="tableSelection(@entangle('selected'), @entangle('idsOnPage'), {{ json_encode($goods->pluck('id')) }})">
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
                    :label="__('goods.name')"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <x-tables.th>{{ __('goods.description') }}</x-tables.th>

                <x-tables.th>{{ __('goods.default_unit') }}</x-tables.th>

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
            @foreach($goods as $good)
                <tr wire:key="good-row-{{ $good->id }}">
                    <x-tables.td>
                        <x-form.input.checkbox name="check_{{ $good->id }}" value="{{ $good->id }}" x-model="selected" wire:key="checkbox-{{ $good->id }}"/>
                    </x-tables.td>
                    <x-tables.td>{{ $good->id }}</x-tables.td>
                    <x-tables.td>{{ $good->name }}</x-tables.td>
                    <td class="px-4 py-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $good->description ?? '-' }}</div>
                    </td>
                    <x-tables.td>{{ $good->defaultUnit?->name ?? '-' }}</x-tables.td>
                    <x-tables.td>
                        <x-form.input.toggle wire:change="toggleActive({{ $good->id }})"
                                             name="{{ $good->id }}" :isActive="$good->is_active" wire:key="toggle-{{ $good->id }}"/>
                    </x-tables.td>
                    <x-tables.td class="flex space-x-2">
                        <x-tables.action-show :route="route('goods.show', $good->id)"/>
                        <x-tables.action-edit :route="route('goods.edit', $good->id)"/>
                        <x-tables.action-delete
                            wire:click="deleteGood({{ $good->id }})"
                            :confirm="__('goods.confirm_delete_good')"
                        />
                    </x-tables.td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <x-slot:footer>
        <x-tables.pagination-footer :paginator="$goods"/>
    </x-slot:footer>
</x-tables.card>
