@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('profile.title') }}"
        :breadcrumbs="[
            __('profile.title') => null,
        ]"
    />

    <livewire:profile.profile-overview />
@endsection
