<?php

namespace App\Listeners;

use App\Models\LoginAuditLog;
use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    public function handle(Failed $event): void
    {
        LoginAuditLog::create([
            'email' => $event->credentials['email'] ?? '',
            'successful' => false,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
