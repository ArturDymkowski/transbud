@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('vehicles.create_title') }}"
        :breadcrumbs="[
            __('vehicles.plural_model_label') => route('vehicles.index'),
            __('labels.tables.create') => null
        ]"
    />
    <livewire:forms.vehicles-form :vehicle="$vehicle"/>
@endsection
