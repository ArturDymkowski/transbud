@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ $delivery->number }}"
        :breadcrumbs="[
        __('deliveries.plural_model_label') => route('deliveries.index'),
        __('labels.tables.show') => null
    ]"
    />
    <livewire:shows.deliveries-show :delivery="$delivery"/>
@endsection
