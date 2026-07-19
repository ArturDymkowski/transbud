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
    <livewire:forms.vehicles-form :vehicle="$vehicle"/>
@endsection
