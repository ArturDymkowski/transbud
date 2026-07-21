@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('address_book.create_title') }}"
        :breadcrumbs="[
            __('address_book.plural_model_label') => route('contractor-addresses.index'),
            __('labels.tables.create') => null
        ]"
    />
    <livewire:forms.contractor-addresses-form :contractor-address="$contractorAddress"/>
@endsection
