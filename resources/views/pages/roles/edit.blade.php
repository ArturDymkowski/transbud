@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ $role->name }}"
        :breadcrumbs="[
        __('roles.plural_model_label') => route('roles.index'),
        __('labels.tables.edit') => null
    ]"
    />
    <livewire:forms.roles-form :role="$role"/>
@endsection
