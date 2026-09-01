<x-tables.card>
    <x-slot:header>
        <x-tables.filter-bar searchModel="search" :placeholder="__('login_audit_log.search_placeholder')">
            <x-form.input.select :label="__('login_audit_log.status')" :options="$this->successfulOptions" name="successful" wire:model.live="successful"/>
        </x-tables.filter-bar>
    </x-slot:header>

    <div class="max-w-full px-5 overflow-x-auto">
        <x-tables.filter-badges :filters="$this->activeFilters"/>

        <table class="min-w-full">
            <thead>
            <tr class="border-gray-200 border-y dark:border-gray-700">
                <x-tables.th-sort
                    field="id"
                    label="ID"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <x-tables.th>{{ __('login_audit_log.email') }}</x-tables.th>

                <x-tables.th>{{ __('login_audit_log.status') }}</x-tables.th>

                <x-tables.th>{{ __('login_audit_log.ip') }}</x-tables.th>

                <x-tables.th>{{ __('login_audit_log.user_agent') }}</x-tables.th>

                <x-tables.th-sort
                    field="created_at"
                    :label="__('login_audit_log.created_at')"
                    :sortField="$sortField"
                    :sortDirection="$sortDirection"
                />

                <x-tables.th>{{ __('login_audit_log.logout_at') }}</x-tables.th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($logs as $log)
                <tr wire:key="login-audit-log-row-{{ $log->id }}">
                    <x-tables.td>{{ $log->id }}</x-tables.td>
                    <x-tables.td>{{ $log->email }}</x-tables.td>
                    <x-tables.td>
                        <x-ui.status-badge :color="$log->successful ? '#12b76a' : '#f04438'">
                            {{ $log->successful ? __('login_audit_log.successful') : __('login_audit_log.failed') }}
                        </x-ui.status-badge>
                    </x-tables.td>
                    <x-tables.td>{{ $log->ip ?? '-' }}</x-tables.td>
                    <x-tables.td class="max-w-xs truncate">{{ $log->user_agent ?? '-' }}</x-tables.td>
                    <x-tables.td>{{ $log->created_at?->format('Y-m-d H:i:s') }}</x-tables.td>
                    <x-tables.td>
                        @if($log->logout_at)
                            {{ $log->logout_at->format('Y-m-d H:i:s') }}
                        @elseif($log->successful)
                            <span class="text-gray-400">{{ __('login_audit_log.session_active') }}</span>
                        @else
                            -
                        @endif
                    </x-tables.td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        {{ __('labels.tables.no_results') }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <x-slot:footer>
        <x-tables.pagination-footer :paginator="$logs"/>
    </x-slot:footer>
</x-tables.card>
