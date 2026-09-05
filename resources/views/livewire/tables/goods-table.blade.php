<x-tables.card :createRoute="auth()->user()->can('goods.create') ? route('goods.create') : null">
    <x-slot:header>
        <x-tables.filter-bar searchModel="search">
            <!-- Trashed & Active -->
            <x-tables.filter-trashed-active :trashedOptions="$this->trashedOptions"/>
        </x-tables.filter-bar>
    </x-slot:header>

    <div class="max-w-full px-5 overflow-x-auto" x-data="tableSelection(@entangle('selected'), @entangle('idsOnPage'), {{ json_encode($goods->pluck('id')) }})">
        @can('goods.delete')
            <x-tables.selection-bar deleteAction="deleteSelected" :confirmMessage="__('labels.tables.confirm_delete_selected')"/>
        @endcan
        <x-tables.filter-badges :filters="$this->activeFilters"/>

        <table class="min-w-full max-md:block">
            <thead class="max-md:hidden">
            <tr class="border-gray-200 border-y dark:border-gray-700">
                @can('goods.delete')
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
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 max-md:block max-md:divide-y-0 max-md:space-y-4">
            @foreach($goods as $good)
                <tr wire:key="good-row-{{ $good->id }}" class="max-md:block max-md:space-y-3 max-md:rounded-xl max-md:border max-md:border-gray-200 max-md:p-4 max-md:divide-y max-md:divide-gray-100 dark:max-md:border-gray-700 dark:max-md:divide-gray-800">
                    @can('goods.delete')
                        <x-tables.td>
                            <x-form.input.checkbox name="check_{{ $good->id }}" value="{{ $good->id }}" x-model="selected" wire:key="checkbox-{{ $good->id }}"/>
                        </x-tables.td>
                    @endcan
                    <x-tables.td label="ID">{{ $good->id }}</x-tables.td>
                    <x-tables.td :label="__('goods.name')">{{ $good->name }}</x-tables.td>
                    <x-tables.td :label="__('goods.description')">{{ $good->description ?? '-' }}</x-tables.td>
                    <x-tables.td :label="__('goods.default_unit')">{{ $good->defaultUnit?->name ?? '-' }}</x-tables.td>
                    <x-tables.td :label="__('labels.tables.active')">
                        @if($good->trashed())
                            <span class="text-gray-400">-</span>
                        @else
                            <x-form.input.toggle wire:change="toggleActive({{ $good->id }})"
                                                 name="{{ $good->id }}" :isActive="$good->is_active" wire:key="toggle-{{ $good->id }}"
                                                 :disabled="! auth()->user()?->can('goods.edit')"/>
                        @endif
                    </x-tables.td>
                    <x-tables.td class="flex space-x-2">
                        <x-tables.action-show :route="route('goods.show', $good->id)"/>
                        @if($good->trashed())
                            @can('goods.edit')
                                <x-tables.action-restore
                                    wire:click="restoreGood({{ $good->id }})"
                                    :confirm="__('labels.tables.confirm_restore')"
                                />
                            @endcan
                            @can('goods.delete')
                                <x-tables.action-delete
                                    wire:click="forceDeleteGood({{ $good->id }})"
                                    :confirm="__('labels.tables.confirm_force_delete')"
                                    :label="__('labels.tables.force_delete')"
                                >
                                    <x-heroicon-o-x-mark class="w-6 h-6 hover:text-red-500"/>
                                </x-tables.action-delete>
                            @endcan
                        @else
                            @can('goods.edit')
                                <x-tables.action-edit :route="route('goods.edit', $good->id)"/>
                            @endcan
                            @can('goods.delete')
                                <x-tables.action-delete
                                    wire:click="deleteGood({{ $good->id }})"
                                    :confirm="__('goods.confirm_delete_good')"
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
        <x-tables.pagination-footer :paginator="$goods"/>
    </x-slot:footer>
</x-tables.card>
