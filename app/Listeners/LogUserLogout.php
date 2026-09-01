<?php

namespace App\Listeners;

use App\Models\LoginAuditLog;
use Illuminate\Auth\Events\Logout;

/**
 * Closes the login_audit_log row for the session that's logging out — see
 * LogSuccessfulLogin, which stamps `session_id` on the way in. The session ID is
 * stable between login and logout here (LoginController never calls
 * session()->regenerate()), so it reliably identifies "this exact browser session"
 * even with several concurrent logins (different tabs/devices) sharing one email.
 *
 * If no matching open row is found (e.g. a session predating this feature, or one
 * that was already closed), this is a silent no-op — there's nothing to fix.
 */
class LogUserLogout
{
    public function handle(Logout $event): void
    {
        LoginAuditLog::query()
            ->where('session_id', session()->getId())
            ->whereNull('logout_at')
            ->latest('id')
            ->first()
            ?->update(['logout_at' => now()]);
    }
}
