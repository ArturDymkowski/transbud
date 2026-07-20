@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('address_book.plural_model_label') }}"
        :breadcrumbs="[
        __('address_book.plural_model_label') => route('contractor-addresses.index'),
        __('labels.tables.list') => null
    ]"
    />
    <livewire:tables.contractor-addresses-table />
@endsection
