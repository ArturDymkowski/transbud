@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ $delivery->number }}"
        :breadcrumbs="[
        __('deliveries.plural_model_label') => route('deliveries.index'),
        __('labels.tables.edit') => null
    ]"
    />
    <x-common.tabs :tabs="[
        'edit' => __('labels.tables.edit'),
        'profitability' => __('deliveries.profitability.tab'),
    ]">
        <x-slot:edit>
            <livewire:forms.deliveries-form :delivery="$delivery"/>
        </x-slot:edit>
        <x-slot:profitability>
            <livewire:profitability.delivery-profitability-panel :delivery="$delivery" :editable="true"/>
        </x-slot:profitability>
    </x-common.tabs>
@endsection
