@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{!! $vehicle->registration_number !!}"
        :breadcrumbs="[
        __('vehicles.plural_model_label') => route('vehicles.index'),
        __('labels.tables.show') => null
    ]"
    />
    <livewire:shows.vehicles-show :vehicle="$vehicle"/>
@endsection
