@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('goods.singular_model_label') }}"
        :breadcrumbs="[
        __('goods.plural_model_label') => route('goods.index'),
        __('labels.tables.list') => null
    ]"
    />
    <livewire:tables.goods-table />
@endsection
