@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{!! $contractor->name !!}"
        :breadcrumbs="[
        __('contractors.plural_model_label') => route('contractors.index'),
        __('contractors.singular_model_label') => route('contractors.edit', ['contractor' => $contractor]),
        __('labels.tables.edit') => null
    ]"
    />
    <x-common.tabs :tabs="[
        'edit' => __('labels.tables.edit'),
        'addresses' => __('address_book.plural_model_label'),
    ]">
        <x-slot:edit>
            <livewire:forms.contractors-form :contractor="$contractor"/>
        </x-slot:edit>
        <x-slot:addresses>
            <livewire:tables.contractor-addresses-table :contractor="$contractor"/>
        </x-slot:addresses>
    </x-common.tabs>
@endsection
