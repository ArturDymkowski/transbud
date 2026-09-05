@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ $permission->name }}"
        :breadcrumbs="[
        __('permissions.plural_model_label') => route('permissions.index'),
        __('labels.tables.edit') => null
    ]"
    />
    <livewire:forms.permissions-form :permission="$permission"/>
@endsection
