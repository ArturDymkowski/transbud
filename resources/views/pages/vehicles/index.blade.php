@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('vehicles.singular_model_label') }}"
        :breadcrumbs="[
        __('vehicles.plural_model_label') => route('vehicles.index'),
        __('labels.tables.list') => null
    ]"
    />
    <livewire:tables.vehicles-table />
@endsection
