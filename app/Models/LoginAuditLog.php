<?php

namespace App\Models;

use Database\Factories\LoginAuditLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * One row per login attempt (successful or not), written exclusively by
 * App\Listeners\LogSuccessfulLogin / LogFailedLogin / LogUserLogout in response to
 * Laravel's own auth events — never written to directly from a controller/Livewire
 * component.
 *
 * A successful login's row is later updated with `logout_at` by LogUserLogout, once
 * the same browser session logs out (matched via `session_id`) — one row per
 * session, not a separate "logout event" row. `logout_at` staying null just means
 * the session was never explicitly logged out of (browser closed, expired, ...).
 *
 * Deliberately has no web-facing route beyond the super-admin-only listing (see
 * App\Http\Middleware\EnsureSuperAdmin) — TODO.md / RISKS.md R18.
 */
class LoginAuditLog extends Model
{
    /** @use HasFactory<LoginAuditLogFactory> */
    use HasFactory;

    public const UPDATED_AT = null;

    protected $table = 'login_audit_log';

    protected $fillable = [
        'email',
        'successful',
        'ip',
        'user_agent',
        'session_id',
        'logout_at',
    ];

    protected $casts = [
        'successful' => 'boolean',
        'logout_at' => 'datetime',
    ];
}
