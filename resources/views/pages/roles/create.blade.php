@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('roles.create_title') }}"
        :breadcrumbs="[
            __('roles.plural_model_label') => route('roles.index'),
            __('labels.tables.create') => null
        ]"
    />
    <livewire:forms.roles-form :role="$role"/>
@endsection
