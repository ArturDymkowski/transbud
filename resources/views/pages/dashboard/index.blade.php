@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('labels.dashboard.title') }}"
        :breadcrumbs="[
        __('labels.dashboard.title') => null
    ]"
    />

    <div class="rounded-2xl border border-gray-200 bg-white p-10 text-center dark:border-gray-800 dark:bg-white/[0.03]">
        <x-heroicon-o-clock class="mx-auto mb-4 h-10 w-10 text-gray-400 dark:text-gray-600" />
        <h3 class="mb-1 text-base font-semibold text-gray-800 dark:text-white/90">
            {{ __('labels.dashboard.empty_title') }}
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('labels.dashboard.empty_description') }}
        </p>
    </div>
@endsection
