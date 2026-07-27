@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{!! $role->name !!}"
        :breadcrumbs="[
        __('roles.plural_model_label') => route('roles.index'),
        __('roles.singular_model_label') => route('roles.edit', ['role' => $role]),
        __('labels.tables.edit') => null
    ]"
    />
    <livewire:forms.roles-form :role="$role"/>
@endsection
