@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ $delivery->number }}"
        :breadcrumbs="[
        __('deliveries.plural_model_label') => route('deliveries.index'),
        __('labels.tables.show') => null
    ]"
    />
    <x-common.tabs :tabs="[
        'view' => __('labels.tables.show'),
        'profitability' => __('deliveries.profitability.tab'),
        'status_history' => __('deliveries.status_history.tab'),
    ]">
        <x-slot:view>
            <livewire:shows.deliveries-show :delivery="$delivery"/>
        </x-slot:view>
        <x-slot:profitability>
            <livewire:profitability.delivery-profitability-panel :delivery="$delivery" :editable="false"/>
        </x-slot:profitability>
        <x-slot:status_history>
            @php
                $statusHistories = $delivery->transportSets
                    ->loadMissing('statusHistories.changedBy')
                    ->flatMap(fn ($transportSet) => $transportSet->statusHistories->map(fn ($history) => [
                        'transport_set' => $transportSet,
                        'history' => $history,
                    ]))
                    ->sortByDesc(fn ($entry) => $entry['history']->created_at)
                    ->values();
            @endphp

            <x-form.section title="{{ __('deliveries.status_history.tab') }}">
                <div class="max-w-full overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                        <tr class="border-gray-200 border-y dark:border-gray-700">
                            <x-tables.th>{{ __('deliveries.status_history.transport_set') }}</x-tables.th>
                            <x-tables.th>{{ __('deliveries.status_history.status') }}</x-tables.th>
                            <x-tables.th>{{ __('deliveries.status_history.changed_by') }}</x-tables.th>
                            <x-tables.th>{{ __('deliveries.status_history.changed_at') }}</x-tables.th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($statusHistories as $entry)
                            <tr wire:key="status-history-{{ $entry['history']->id }}">
                                <x-tables.td>
                                    @php
                                        $transportSetLabel = collect([
                                            $entry['transport_set']->driver?->name,
                                            $entry['transport_set']->vehicle?->registration_number,
                                            $entry['transport_set']->trailer?->registration_number,
                                        ])->filter()->implode(', ');
                                    @endphp
                                    {{ $transportSetLabel !== '' ? $transportSetLabel : ('#'.$entry['transport_set']->id) }}
                                </x-tables.td>
                                <x-tables.td>{{ $entry['history']->status->label() }}</x-tables.td>
                                <x-tables.td>{{ $entry['history']->changedBy->name ?? __('deliveries.status_history.system') }}</x-tables.td>
                                <x-tables.td>{{ $entry['history']->created_at?->format('Y-m-d H:i') }}</x-tables.td>
                            </tr>
                        @empty
                            <tr>
                                <x-tables.td>{{ __('deliveries.status_history.empty') }}</x-tables.td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-form.section>
        </x-slot:status_history>
    </x-common.tabs>
@endsection
