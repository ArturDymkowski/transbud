@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ $user->name }}"
        :breadcrumbs="[
        __('users.plural_model_label') => route('users.index'),
        __('labels.tables.edit') => null
    ]"
    />
    <livewire:forms.users-form :user="$user"/>
@endsection
