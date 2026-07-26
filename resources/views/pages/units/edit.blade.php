@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{!! $unit->name !!}"
        :breadcrumbs="[
        __('units.plural_model_label') => route('units.index'),
        __('units.singular_model_label') => route('units.edit', ['unit' => $unit]),
        __('labels.tables.edit') => null
    ]"
    />
    <livewire:forms.units-form :unit="$unit"/>
@endsection
