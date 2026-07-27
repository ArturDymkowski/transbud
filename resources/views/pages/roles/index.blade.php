@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('roles.singular_model_label') }}"
        :breadcrumbs="[
        __('roles.plural_model_label') => route('roles.index'),
        __('labels.tables.list') => null
    ]"
    />
    <livewire:tables.roles-table />
@endsection
