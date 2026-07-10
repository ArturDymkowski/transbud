@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('drivers.create_title') }}"
        :breadcrumbs="[
            __('drivers.plural_model_label') => route('drivers.index'),
            __('labels.tables.create') => null
        ]"
    />
    <livewire:forms.drivers-form :driver="$driver"/>
@endsection
