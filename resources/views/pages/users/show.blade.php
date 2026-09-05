@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ $user->name }}"
        :breadcrumbs="[
        __('users.plural_model_label') => route('users.index'),
        __('labels.tables.show') => null
    ]"
    />
    <livewire:shows.users-show :user="$user"/>
@endsection
