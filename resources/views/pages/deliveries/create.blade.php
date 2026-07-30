@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('deliveries.create_title') }}"
        :breadcrumbs="[
            __('deliveries.plural_model_label') => route('deliveries.index'),
            __('labels.tables.create') => null
        ]"
    />
    <livewire:forms.deliveries-form :delivery="$delivery"/>
@endsection
