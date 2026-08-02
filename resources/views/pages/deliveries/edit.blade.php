@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ $delivery->number }}"
        :breadcrumbs="[
        __('deliveries.plural_model_label') => route('deliveries.index'),
        __('deliveries.singular_model_label') => route('deliveries.edit', ['delivery' => $delivery]),
        __('labels.tables.edit') => null
    ]"
    />
    <livewire:forms.deliveries-form :delivery="$delivery"/>
@endsection
