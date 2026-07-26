@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('goods.create_title') }}"
        :breadcrumbs="[
            __('goods.plural_model_label') => route('goods.index'),
            __('labels.tables.create') => null
        ]"
    />
    <livewire:forms.goods-form :good="$good"/>
@endsection
