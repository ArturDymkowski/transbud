@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{!! $driver->name !!}"
        :breadcrumbs="[
        __('drivers.plural_model_label') => route('drivers.index'),
        __('labels.tables.show') => null
    ]"
    />
    <livewire:forms.drivers-show :driver="$driver"/>
@endsection
