@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{!! $permission->name !!}"
        :breadcrumbs="[
        __('permissions.plural_model_label') => route('permissions.index'),
        __('permissions.singular_model_label') => route('permissions.edit', ['permission' => $permission]),
        __('labels.tables.edit') => null
    ]"
    />
    <livewire:forms.permissions-form :permission="$permission"/>
@endsection
