@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Kierowcy" />
    <livewire:forms.drivers-form :editingDriver="$driver"/>
@endsection
