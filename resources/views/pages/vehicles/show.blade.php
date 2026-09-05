@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ $vehicle->registration_number }}"
        :breadcrumbs="[
        __('vehicles.plural_model_label') => route('vehicles.index'),
        __('labels.tables.show') => null
    ]"
    />
    <x-common.tabs :tabs="[
        'view' => __('labels.tables.show'),
        'drivers' => __('vehicles.assigned_drivers'),
    ]">
        <x-slot:view>
            <livewire:shows.vehicles-show :vehicle="$vehicle"/>
        </x-slot:view>
        <x-slot:drivers>
            <livewire:tables.drivers-table :vehicle="$vehicle" :readonly="true"/>
        </x-slot:drivers>
    </x-common.tabs>
@endsection
