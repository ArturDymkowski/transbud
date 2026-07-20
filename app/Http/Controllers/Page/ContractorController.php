<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;

class ContractorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.contractors.index');
    }
}
