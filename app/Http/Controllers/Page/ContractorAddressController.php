<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;

class ContractorAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.contractor-addresses.index');
    }
}
