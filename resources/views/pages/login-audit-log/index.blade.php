@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('login_audit_log.plural_model_label') }}"
        :breadcrumbs="[
        __('login_audit_log.plural_model_label') => route('login-audit-log.index'),
        __('labels.tables.list') => null
    ]"
    />
    <livewire:tables.login-audit-log-table />
@endsection
