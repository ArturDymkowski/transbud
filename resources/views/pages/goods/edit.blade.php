@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ $good->name }}"
        :breadcrumbs="[
        __('goods.plural_model_label') => route('goods.index'),
        __('labels.tables.edit') => null
    ]"
    />
    <x-common.tabs :tabs="[
        'edit' => __('labels.tables.edit'),
        'units' => __('goods.units'),
    ]">
        <x-slot:edit>
            <livewire:forms.goods-form :good="$good"/>
        </x-slot:edit>
        <x-slot:units>
            <livewire:tables.units-table :good="$good"/>
        </x-slot:units>
    </x-common.tabs>
@endsection
