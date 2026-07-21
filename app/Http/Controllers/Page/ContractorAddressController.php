<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;
use App\Models\ContractorAddress;

class ContractorAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.contractor-addresses.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.contractor-addresses.create', ['contractorAddress' => new ContractorAddress()]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContractorAddress $contractorAddress)
    {
        return view('pages.contractor-addresses.edit', ['contractorAddress' => $contractorAddress]);
    }
}
