@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('users.singular_model_label') }}"
        :breadcrumbs="[
        __('users.plural_model_label') => route('users.index'),
        __('labels.tables.list') => null
    ]"
    />
    <livewire:tables.users-table />
@endsection
