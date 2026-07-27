@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('permissions.singular_model_label') }}"
        :breadcrumbs="[
        __('permissions.plural_model_label') => route('permissions.index'),
        __('labels.tables.list') => null
    ]"
    />
    <livewire:tables.permissions-table />
@endsection
