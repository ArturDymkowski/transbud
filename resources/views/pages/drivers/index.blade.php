@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('drivers.singular_model_label') }}"
        :breadcrumbs="[
        __('drivers.plural_model_label') => route('drivers.index'),
        __('labels.tables.list') => null
    ]"
    />
    <livewire:tables.drivers-table />
@endsection
