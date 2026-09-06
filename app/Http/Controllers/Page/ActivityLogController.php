<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index()
    {
        return view('pages.activity-log.index');
    }

    public function show(Activity $activity)
    {
        return view('pages.activity-log.show', ['activity' => $activity]);
    }
}
