<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;

class LoginAuditLogController extends Controller
{
    /**
     * Display the login history. Access is restricted by the `super-admin`
     * middleware on the route, not by a Spatie permission — see
     * App\Http\Middleware\EnsureSuperAdmin.
     */
    public function index()
    {
        return view('pages.login-audit-log.index');
    }
}
