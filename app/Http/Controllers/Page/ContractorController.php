<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;
use App\Models\Contractor;

class ContractorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.contractors.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.contractors.create', ['contractor' => new Contractor()]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contractor $contractor)
    {
        return view('pages.contractors.edit', ['contractor' => $contractor]);
    }
}
