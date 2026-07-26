@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{!! $unit->name !!}"
        :breadcrumbs="[
        __('units.plural_model_label') => route('units.index'),
        __('labels.tables.show') => null
    ]"
    />
    <livewire:shows.units-show :unit="$unit"/>
@endsection
