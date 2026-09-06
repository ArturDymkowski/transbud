@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('activity_log.singular_model_label') }} #{{ $activity->id }}"
        :breadcrumbs="[
        __('activity_log.plural_model_label') => route('activity-log.index'),
        __('labels.tables.show') => null
    ]"
    />
    <livewire:shows.activity-log-show :activity="$activity"/>
@endsection
