<x-tables.card :createRoute="route('contractors.create')">
    <x-slot:header>
        <x-tables.filter-bar searchModel="search">
            <!-- Trashed -->
            <x-form.input.select :label="__('labels.tables.trashed')" :options="$this->trashedOptions" name="trashed" wire:model.live="trashed"/>

            <!-- Active -->
            <x-form.input.select :label="__('labels.tables.active')" :options="[
			         '' => __('labels.tables.all'),
                         0 => __('labels.tables.no'),
                         1 => __('labels.tables.yes')
			]" name="active" wire:model.live="active"/>
        </x-tables.filter-bar>
    </x-slot:header>

    <div class="max-w-full px-5 overflow-x-auto" x-data="tableSelection(@entangle('selected'), @entangle('idsOnPage'), {{ json_encode($contractors->pluck('id')) }})">
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

                <x-tables.th-sort
                    field="name"
                    :label="__('contractors.name')"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <th scope="col"
                    class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                    {{ __('contractors.email') }}
                </th>

                <th scope="col"
                    class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                    {{ __('contractors.phone') }}
                </th>

                <th scope="col"
                    class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                    {{ __('contractors.nip') }}
                </th>

                <th scope="col"
                    class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                    {{ __('contractors.regon') }}
                </th>

                <x-tables.th-sort
                    field="active"
                    :label="__('labels.tables.active')"
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
            @foreach($contractors as $contractor)
                <tr wire:key="contractor-row-{{ $contractor->id }}">
                    <x-tables.td>
                        <x-form.input.checkbox name="check_{{ $contractor->id }}" value="{{ $contractor->id }}" x-model="selected" wire:key="checkbox-{{ $contractor->id }}"/>
                    </x-tables.td>
                    <x-tables.td>{{ $contractor->id }}</x-tables.td>
                    <x-tables.td>{{ $contractor->name ?? '-' }}</x-tables.td>
                    <x-tables.td>{{ $contractor->email ?? '-' }}</x-tables.td>
                    <x-tables.td>{{ $contractor->phone ?? '-' }}</x-tables.td>
                    <x-tables.td>{{ $contractor->nip ?? '-' }}</x-tables.td>
                    <x-tables.td>{{ $contractor->regon ?? '-' }}</x-tables.td>
                    <x-tables.td>
                        <x-form.input.toggle wire:change="toggleActive({{ $contractor->id }})"
                                             name="{{ $contractor->id }}" :isActive="$contractor->active" wire:key="toggle-{{ $contractor->id }}"/>
                    </x-tables.td>
                    <x-tables.td class="flex space-x-2">
                        <x-ui.tooltip :text="__('labels.tables.show')">
                            <a href="{{ route('contractors.show', $contractor->id) }}" wire:navigate>
                                <x-heroicon-o-eye class="w-6 h-6 hover:text-brand-500"/>
                            </a>
                        </x-ui.tooltip>
                        <x-ui.tooltip :text="__('labels.tables.edit')">
                            <a href="{{ route('contractors.edit', $contractor->id) }}" wire:navigate>
                                <x-heroicon-o-pencil-square class="w-6 h-6 hover:text-green-500"/>
                            </a>
                        </x-ui.tooltip>
                        <x-ui.tooltip :text="__('labels.tables.delete')">
                            <button type="button"
                                    wire:click="deleteContractor({{ $contractor->id }})"
                                    wire:confirm="{{ __('contractors.confirm_delete_contractor') }}"
                            >
                                <x-heroicon-o-trash class="w-6 h-6 hover:text-red-500"/>
                            </button>
                        </x-ui.tooltip>
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
