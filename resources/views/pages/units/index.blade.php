@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('units.singular_model_label') }}"
        :breadcrumbs="[
        __('units.plural_model_label') => route('units.index'),
        __('labels.tables.list') => null
    ]"
    />
    <livewire:tables.units-table />
@endsection
