@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{!! $contractorAddress->contractor->name ?? '-' !!}"
        :breadcrumbs="[
        __('address_book.plural_model_label') => route('contractor-addresses.index'),
        __('address_book.singular_model_label') => route('contractor-addresses.edit', ['contractor_address' => $contractorAddress]),
        __('labels.tables.edit') => null
    ]"
    />
    <livewire:forms.contractor-addresses-form :contractor-address="$contractorAddress"/>
@endsection
