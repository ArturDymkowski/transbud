<?php

namespace App\Listeners;

use App\Models\LoginAuditLog;
use Illuminate\Auth\Events\Login;

/**
 * Records every successful login into login_audit_log. Registered against Laravel's
 * own Login event in AppServiceProvider, so LoginController doesn't need to know
 * anything about auditing — it already fires this event via Auth::attempt().
 *
 * Stamps the row with the current session ID so LogUserLogout can later find and
 * close this exact row when this same browser session logs out.
 */
class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        LoginAuditLog::create([
            'email' => $event->user->email,
            'successful' => true,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
        ]);
    }
}
