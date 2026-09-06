@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('activity_log.plural_model_label') }}"
        :breadcrumbs="[
        __('activity_log.plural_model_label') => route('activity-log.index'),
        __('labels.tables.list') => null
    ]"
    />
    <livewire:tables.activity-log-table />
@endsection
