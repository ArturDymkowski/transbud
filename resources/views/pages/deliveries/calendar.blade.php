@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('deliveries.calendar_title') }}"
        :breadcrumbs="[
        __('deliveries.plural_model_label') => route('deliveries.index'),
        __('deliveries.calendar_title') => null
    ]"
    />

    <x-common.tabs :tabs="[
        'calendar' => __('deliveries.tabs.calendar'),
        'planner' => __('deliveries.tabs.planner'),
    ]" :icons="[
        'calendar' => 'heroicon-o-calendar',
        'planner' => 'heroicon-o-truck',
    ]">
        <x-slot:calendar>
            <livewire:calendars.deliveries-calendar />
        </x-slot:calendar>
        <x-slot:planner>
            <livewire:planners.deliveries-planner />
        </x-slot:planner>
    </x-common.tabs>

    {{-- Single shared instance: opened by both the calendar and the planner via the
         `open-transport-set-modal` browser event, so there is exactly one modal on the page. --}}
    <livewire:modals.transport-set-edit-modal />
@endsection
