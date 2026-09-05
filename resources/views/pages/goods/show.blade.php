@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ $good->name }}"
        :breadcrumbs="[
        __('goods.plural_model_label') => route('goods.index'),
        __('labels.tables.show') => null
    ]"
    />
    <x-common.tabs :tabs="[
        'view' => __('labels.tables.show'),
        'units' => __('goods.units'),
    ]">
        <x-slot:view>
            <livewire:shows.goods-show :good="$good"/>
        </x-slot:view>
        <x-slot:units>
            <livewire:tables.units-table :good="$good" :readonly="true"/>
        </x-slot:units>
    </x-common.tabs>
@endsection
