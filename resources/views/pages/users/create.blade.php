@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('users.create_title') }}"
        :breadcrumbs="[
            __('users.plural_model_label') => route('users.index'),
            __('labels.tables.create') => null
        ]"
    />
    <livewire:forms.users-form :user="$user"/>
@endsection
