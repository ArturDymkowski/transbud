@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{!! $contractor->name !!}"
        :breadcrumbs="[
        __('contractors.plural_model_label') => route('contractors.index'),
        __('labels.tables.show') => null
    ]"
    />
    <livewire:shows.contractors-show :contractor="$contractor"/>
@endsection
