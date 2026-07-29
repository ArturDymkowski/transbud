@php
    $resourceLabels = [
        'drivers' => __('drivers.plural_model_label'),
        'vehicles' => __('vehicles.plural_model_label'),
        'contractors' => __('contractors.plural_model_label'),
        'contractor-addresses' => __('address_book.plural_model_label'),
        'goods' => __('goods.plural_model_label'),
        'units' => __('units.plural_model_label'),
        'users' => __('users.plural_model_label'),
        'roles' => __('roles.plural_model_label'),
        'permissions' => __('permissions.plural_model_label'),
    ];

    $actionLabels = [
        'view' => __('labels.tables.show'),
        'create' => __('labels.tables.create'),
        'edit' => __('labels.tables.edit'),
        'delete' => __('labels.tables.delete'),
    ];

    $allChecked = $this->allPermissions->isNotEmpty()
        && count($selectedPermissions) === $this->allPermissions->count();

    $permissionCheckboxesScript = <<<'JS'
        {
            permissionCheckboxes(resource = null) {
                const selector = resource ? '[data-resource=\'' + resource + '\']' : '[data-resource]';
                return Array.from($el.querySelectorAll(selector));
            },
            setChecked(boxes, checked) {
                boxes.forEach(cb => { if (cb.checked !== checked) cb.click(); });
            },
            toggleAll() {
                const boxes = this.permissionCheckboxes();
                const allChecked = boxes.length > 0 && boxes.every(cb => cb.checked);
                this.setChecked(boxes, !allChecked);
            },
            toggleGroup(resource) {
                const boxes = this.permissionCheckboxes(resource);
                const allChecked = boxes.length > 0 && boxes.every(cb => cb.checked);
                this.setChecked(boxes, !allChecked);
            },
        }
        JS;
@endphp

<x-form.wrapper :cancelRoute="route('roles.index')" :x-data="$permissionCheckboxesScript">

    <x-form.section title="{{ __('roles.basic_info') }}">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-end">
            <div class="flex-1">
                <x-form.input.text-input name="roleData.name"
                                         label="{{ __('roles.name') }}"
                                         required="true"
                                         wire:model="roleData.name"
                />
            </div>

            <div class="flex items-center gap-3" @change="toggleAll()">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-400">
                    {{ __('roles.select_all_permissions') }}
                </span>
                <x-form.input.toggle name="selectAll" :isActive="$allChecked"/>
            </div>
        </div>
    </x-form.section>

    <x-form.section title="{{ __('roles.permissions_section') }}">
        <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($this->groupedPermissions as $resource => $permissions)
                <div>
                    <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300 cursor-pointer select-none hover:text-brand-500"
                        @click="toggleGroup('{{ $resource }}')"
                    >
                        {{ $resourceLabels[$resource] ?? $resource }}
                    </h3>
                    <div class="flex flex-col gap-2">
                        @foreach($permissions as $permission)
                            @php($action = \Illuminate\Support\Str::after($permission->name, '.'))
                            <x-form.input.checkbox name="permission_{{ $permission->id }}"
                                                    value="{{ $permission->id }}"
                                                    data-resource="{{ $resource }}"
                                                    wire:model="selectedPermissions"
                                                    wire:key="permission-checkbox-{{ $permission->id }}"
                            >
                                {{ $actionLabels[$action] ?? $action }}
                            </x-form.input.checkbox>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </x-form.section>

</x-form.wrapper>
