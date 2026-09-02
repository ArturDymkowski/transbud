<?php

use App\Livewire\Tables\LoginAuditLogTable;
use App\Models\LoginAuditLog;
use App\Models\User;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

test('a successful login is recorded in the audit log exactly once', function () {
    $user = User::factory()->create(['password' => Hash::make('correct-password')]);

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'correct-password',
    ]);

    $this->assertDatabaseHas('login_audit_log', [
        'email' => $user->email,
        'successful' => true,
    ]);
    // Not just assertDatabaseHas: that only proves a matching row exists, not that
    // there's exactly one. This is the regression check for I11 — the listener used
    // to be wired up twice (once via Laravel's own event auto-discovery, which picks
    // up any typed handle() method in app/Listeners automatically, and once more via
    // an unnecessary explicit Event::listen() in AppServiceProvider), so every login
    // silently wrote two identical rows.
    $this->assertDatabaseCount('login_audit_log', 1);
});

test('a failed login attempt is recorded in the audit log exactly once', function () {
    $user = User::factory()->create(['password' => Hash::make('correct-password')]);

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertDatabaseHas('login_audit_log', [
        'email' => $user->email,
        'successful' => false,
    ]);
    $this->assertDatabaseCount('login_audit_log', 1);
});

test('a failed login attempt for an email with no matching account is still recorded exactly once', function () {
    $this->post(route('login.store'), [
        'email' => 'nobody@example.com',
        'password' => 'whatever',
    ]);

    $this->assertDatabaseHas('login_audit_log', [
        'email' => 'nobody@example.com',
        'successful' => false,
    ]);
    $this->assertDatabaseCount('login_audit_log', 1);
});

test('logging out closes the login_audit_log row for the current session', function () {
    $user = User::factory()->create();

    // Stands in for what LogSuccessfulLogin would have written at login time for
    // this exact session — session_id matching the test's own current session lets
    // the Logout event (dispatched directly below, so it runs in this same session
    // context, no HTTP round-trip involved) find and close it.
    $log = LoginAuditLog::factory()->create([
        'email' => $user->email,
        'session_id' => session()->getId(),
        'logout_at' => null,
    ]);

    event(new Logout('web', $user));

    expect($log->fresh()->logout_at)->not->toBeNull();
});

test('logging out does not touch a login_audit_log row belonging to a different session', function () {
    $user = User::factory()->create();

    $otherSessionLog = LoginAuditLog::factory()->create([
        'email' => $user->email,
        'session_id' => 'some-other-browser-session-id',
        'logout_at' => null,
    ]);

    event(new Logout('web', $user));

    expect($otherSessionLog->fresh()->logout_at)->toBeNull();
});

test('logging out does not error when there is no matching login_audit_log row', function () {
    // actingAs() bypasses Auth::attempt() entirely, so no Login event ever fired and
    // no row exists to close — LogUserLogout must handle that gracefully.
    $user = User::factory()->create();

    $this->actingAs($user)
        ->delete(route('login.destroy'))
        ->assertRedirect('/');
});

test('guest is redirected from the login history page', function () {
    $this->get(route('login-audit-log.index'))->assertRedirect(route('login'));
});

test('a regular user, even an Admin, is forbidden from the login history page', function () {
    $admin = actingAsAdmin();
    expect($admin->fresh()->is_super_admin)->toBeFalse();

    $this->get(route('login-audit-log.index'))->assertForbidden();

    Livewire::test(LoginAuditLogTable::class)->assertForbidden();
});

test('a super admin can view the login history page', function () {
    $superAdmin = User::factory()->create(['is_super_admin' => true]);

    $this->actingAs($superAdmin)
        ->get(route('login-audit-log.index'))
        ->assertOk();
});

test('the login history menu item is hidden from a regular Admin', function () {
    actingAsAdmin();

    $this->get(route('dashboard'))->assertDontSee(route('login-audit-log.index'), false);
});

test('the login history menu item is shown to a super admin', function () {
    $superAdmin = User::factory()->create(['is_super_admin' => true]);

    $this->actingAs($superAdmin)
        ->get(route('dashboard'))
        ->assertSee(route('login-audit-log.index'), false);
});

test('the login history table lists recorded attempts and supports the status filter', function () {
    $superAdmin = User::factory()->create(['is_super_admin' => true]);
    $this->actingAs($superAdmin);

    LoginAuditLog::factory()->create(['email' => 'visitor@example.com']);
    LoginAuditLog::factory()->failed()->create(['email' => 'attacker@example.com']);

    Livewire::test(LoginAuditLogTable::class)
        ->assertSee('visitor@example.com')
        ->assertSee('attacker@example.com')
        ->set('successful', '1')
        ->assertSee('visitor@example.com')
        ->assertDontSee('attacker@example.com');
});
