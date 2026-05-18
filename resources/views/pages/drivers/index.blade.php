@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Kierowcy" />
    <livewire:tables.drivers-table />
@endsection
