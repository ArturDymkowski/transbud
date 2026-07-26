@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{!! $contractorAddress->contractor->name ?? '-' !!}"
        :breadcrumbs="[
        __('address_book.plural_model_label') => route('contractor-addresses.index'),
        __('labels.tables.show') => null
    ]"
    />
    <livewire:shows.contractor-addresses-show :contractor-address="$contractorAddress"/>
@endsection
