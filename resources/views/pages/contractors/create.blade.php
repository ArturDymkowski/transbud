@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('contractors.create_title') }}"
        :breadcrumbs="[
            __('contractors.plural_model_label') => route('contractors.index'),
            __('labels.tables.create') => null
        ]"
    />
    <livewire:forms.contractors-form :contractor="$contractor"/>
@endsection
