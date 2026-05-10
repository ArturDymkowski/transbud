@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Kierowcy" />
    <div class="space-y-6">
        @foreach($drivers as $driver)
            <p>{{ $driver->name }}</p>
        @endforeach
    </div>
@endsection
