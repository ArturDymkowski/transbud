@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('units.create_title') }}"
        :breadcrumbs="[
            __('units.plural_model_label') => route('units.index'),
            __('labels.tables.create') => null
        ]"
    />
    <livewire:forms.units-form :unit="$unit"/>
@endsection
