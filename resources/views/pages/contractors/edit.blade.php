@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{!! $contractor->name !!}"
        :breadcrumbs="[
        __('contractors.plural_model_label') => route('contractors.index'),
        __('contractors.singular_model_label') => route('contractors.edit', ['contractor' => $contractor]),
        __('labels.tables.edit') => null
    ]"
    />
    <livewire:forms.contractors-form :contractor="$contractor"/>
@endsection
