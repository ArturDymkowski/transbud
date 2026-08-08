@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{!! $contractor->name !!}"
        :breadcrumbs="[
        __('contractors.plural_model_label') => route('contractors.index'),
        __('labels.tables.show') => null
    ]"
    />
    <x-common.tabs :tabs="[
        'view' => __('labels.tables.show'),
        'addresses' => __('address_book.plural_model_label'),
    ]">
        <x-slot:view>
            <livewire:shows.contractors-show :contractor="$contractor"/>
        </x-slot:view>
        <x-slot:addresses>
            <livewire:tables.contractor-addresses-table :contractor="$contractor" :readonly="true"/>
        </x-slot:addresses>
    </x-common.tabs>
@endsection
