@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{!! $driver->name !!}"
        :breadcrumbs="[
        __('drivers.plural_model_label') => route('drivers.index'),
        __('drivers.singular_model_label') => route('drivers.edit', ['driver' => $driver]),
        __('labels.tables.edit') => null
    ]"
    />
    <livewire:forms.drivers-form :driver="$driver"/>
@endsection
