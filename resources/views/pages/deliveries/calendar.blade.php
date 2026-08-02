@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('deliveries.calendar_title') }}"
        :breadcrumbs="[
        __('deliveries.plural_model_label') => route('deliveries.index'),
        __('deliveries.calendar_title') => null
    ]"
    />
    <livewire:calendars.deliveries-calendar />
@endsection
