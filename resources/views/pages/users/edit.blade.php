@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{!! $user->name !!}"
        :breadcrumbs="[
        __('users.plural_model_label') => route('users.index'),
        __('users.singular_model_label') => route('users.edit', ['user' => $user]),
        __('labels.tables.edit') => null
    ]"
    />
    <livewire:forms.users-form :user="$user"/>
@endsection
