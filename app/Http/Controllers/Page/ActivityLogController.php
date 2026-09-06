<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;

class ActivityLogController extends Controller
{
    public function index()
    {
        return view('pages.activity-log.index');
    }
}
