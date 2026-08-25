<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    /**
     * Show the authenticated user's profile.
     */
    public function edit()
    {
        return view('pages.profile.edit');
    }
}
