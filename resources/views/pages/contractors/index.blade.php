@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('contractors.singular_model_label') }}"
        :breadcrumbs="[
        __('contractors.plural_model_label') => route('contractors.index'),
        __('labels.tables.list') => null
    ]"
    />
    <livewire:tables.contractors-table />
@endsection
