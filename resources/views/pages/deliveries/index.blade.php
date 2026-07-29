@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('deliveries.singular_model_label') }}"
        :breadcrumbs="[
        __('deliveries.plural_model_label') => route('deliveries.index'),
        __('labels.tables.list') => null
    ]"
    />
    <livewire:tables.deliveries-table />
@endsection
