<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;
use App\Models\Delivery;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.deliveries.index');
    }

    /**
     * Display the delivery calendar.
     */
    public function calendar()
    {
        return view('pages.deliveries.calendar');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.deliveries.create', ['delivery' => new Delivery]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Delivery $delivery)
    {
        return view('pages.deliveries.show', ['delivery' => $delivery]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Delivery $delivery)
    {
        return view('pages.deliveries.edit', ['delivery' => $delivery]);
    }
}
