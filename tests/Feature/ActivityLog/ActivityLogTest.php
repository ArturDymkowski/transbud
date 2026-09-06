<?php

use App\Livewire\Tables\ActivityLogTable;
use App\Models\Driver;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Activitylog\Models\Activity;

test('guest is redirected from the activity log page', function () {
    $this->get(route('activity-log.index'))->assertRedirect(route('login'));
});

test('a regular user, even an Admin, is forbidden from the activity log page', function () {
    $admin = actingAsAdmin();
    expect($admin->fresh()->is_super_admin)->toBeFalse();

    $this->get(route('activity-log.index'))->assertForbidden();

    Livewire::test(ActivityLogTable::class)->assertForbidden();
});

test('a super admin can view the activity log page', function () {
    $superAdmin = User::factory()->create(['is_super_admin' => true]);

    $this->actingAs($superAdmin)
        ->get(route('activity-log.index'))
        ->assertOk();
});

test('the activity log menu item is hidden from a regular Admin', function () {
    actingAsAdmin();

    $this->get(route('dashboard'))->assertDontSee(route('activity-log.index'), false);
});

test('the activity log menu item is shown to a super admin', function () {
    $superAdmin = User::factory()->create(['is_super_admin' => true]);

    $this->actingAs($superAdmin)
        ->get(route('dashboard'))
        ->assertSee(route('activity-log.index'), false);
});

test('creating and updating a driver records activity log entries with the causer attached', function () {
    $admin = actingAsAdmin();

    $driver = Driver::factory()->create(['name' => 'Jan Kowalski']);
    $driver->update(['name' => 'Jan Nowak']);

    $created = Activity::query()->forSubject($driver)->forEvent('created')->sole();
    $updated = Activity::query()->forSubject($driver)->forEvent('updated')->sole();

    expect($created->log_name)->toBe('drivers')
        ->and($created->causer_id)->toBe($admin->id)
        ->and($updated->attribute_changes['old']['name'])->toBe('Jan Kowalski')
        ->and($updated->attribute_changes['attributes']['name'])->toBe('Jan Nowak');
});

test('changing a user password is never written to the activity log', function () {
    actingAsAdmin();

    $user = User::factory()->create();
    $user->update(['password' => 'a-new-hashed-value']);

    $updated = Activity::query()->forSubject($user)->forEvent('updated')->sole();

    expect($updated->attribute_changes['attributes'] ?? [])->not->toHaveKey('password')
        ->and($updated->attribute_changes['old'] ?? [])->not->toHaveKey('password');
});

test('renaming a role records an activity log entry', function () {
    actingAsAdmin();

    $role = Role::create(['name' => 'Dispatcher']);
    $role->update(['name' => 'Senior Dispatcher']);

    $updated = Activity::query()->forSubject($role)->forEvent('updated')->sole();

    expect($updated->log_name)->toBe('roles')
        ->and($updated->attribute_changes['attributes']['name'])->toBe('Senior Dispatcher');
});

test('the activity log table lists entries and supports the resource and event filters', function () {
    $superAdmin = User::factory()->create(['is_super_admin' => true]);
    $this->actingAs($superAdmin);

    $driver = Driver::factory()->create(['name' => 'Jan Kowalski']);
    $role = Role::create(['name' => 'Dispatcher']);

    Livewire::test(ActivityLogTable::class)
        ->assertSee('Jan Kowalski')
        ->assertSee('Dispatcher')
        ->set('logName', 'drivers')
        ->assertSee('Jan Kowalski')
        ->assertDontSee('Dispatcher')
        ->set('logName', '')
        ->set('event', 'updated')
        ->assertDontSee('Jan Kowalski')
        ->assertDontSee('Dispatcher');
});
