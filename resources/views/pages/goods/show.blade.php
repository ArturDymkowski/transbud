@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{!! $good->name !!}"
        :breadcrumbs="[
        __('goods.plural_model_label') => route('goods.index'),
        __('labels.tables.show') => null
    ]"
    />
    <livewire:shows.goods-show :good="$good"/>
@endsection
