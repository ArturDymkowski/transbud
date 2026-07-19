@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{!! $vehicle->registration_number !!}"
        :breadcrumbs="[
        __('vehicles.plural_model_label') => route('vehicles.index'),
        __('vehicles.singular_model_label') => route('vehicles.edit', ['vehicle' => $vehicle]),
        __('labels.tables.edit') => null
    ]"
    />
    <x-common.tabs :tabs="[
        'edit' => __('labels.tables.edit'),
        'drivers' => __('vehicles.assigned_drivers'),
    ]">
        <x-slot:edit>
            <livewire:forms.vehicles-form :vehicle="$vehicle"/>
        </x-slot:edit>
        <x-slot:drivers>
            <livewire:tables.vehicle-drivers-table :vehicle="$vehicle"/>
        </x-slot:drivers>
    </x-common.tabs>
@endsection
