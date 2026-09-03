<?php

use App\Livewire\Tables\UsersTable;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    actingAsAdmin();
});

test('guest is redirected from the users list', function () {
    auth()->logout();

    $this->get(route('users.index'))->assertRedirect(route('login'));
});

test('users index page lists users', function () {
    User::factory()->create(['name' => 'Jan Kowalski']);

    $this->get(route('users.index'))->assertOk()->assertSee('Jan Kowalski');
});

test('search filters users by name', function () {
    User::factory()->create(['name' => 'Jan Kowalski']);
    User::factory()->create(['name' => 'Anna Nowak']);

    Livewire::test(UsersTable::class)
        ->set('search', 'Kowalski')
        ->assertSee('Jan Kowalski')
        ->assertDontSee('Anna Nowak');
});

test('search filters users by email', function () {
    User::factory()->create(['name' => 'Jan Kowalski', 'email' => 'jan@example.com']);
    User::factory()->create(['name' => 'Anna Nowak', 'email' => 'anna@example.com']);

    Livewire::test(UsersTable::class)
        ->set('search', 'jan@example.com')
        ->assertSee('Jan Kowalski')
        ->assertDontSee('Anna Nowak');
});

test('the assigned role is shown in the role column', function () {
    $role = Role::create(['name' => 'Dispatcher']);
    $user = User::factory()->create(['name' => 'Jan Kowalski']);
    $user->assignRole($role);

    User::factory()->create(['name' => 'Anna Nowak']);

    $component = Livewire::test(UsersTable::class);

    $component->assertSeeInOrder(['Jan Kowalski', 'Dispatcher']);
    $component->assertSeeInOrder(['Anna Nowak', '-']);
});

test('active filter narrows the list to active or inactive users', function () {
    User::factory()->create(['name' => 'ActiveUser', 'is_active' => true]);
    User::factory()->create(['name' => 'InactiveUser', 'is_active' => false]);

    Livewire::test(UsersTable::class)
        ->set('isActive', '1')
        ->assertSee('ActiveUser')
        ->assertDontSee('InactiveUser');
});

test('trashed filter shows only soft deleted users', function () {
    User::factory()->create(['name' => 'KeepMe']);
    $deleted = User::factory()->create(['name' => 'DeletedUser']);
    $deleted->delete();

    Livewire::test(UsersTable::class)
        ->set('trashed', 'only')
        ->assertSee('DeletedUser')
        ->assertDontSee('KeepMe');
});

test('toggleActive flips the is_active flag', function () {
    $user = User::factory()->create(['is_active' => true]);

    Livewire::test(UsersTable::class)->call('toggleActive', $user->id);

    expect($user->refresh()->is_active)->toBeFalse();
});

test('deleteUser removes a single user', function () {
    $user = User::factory()->create();

    Livewire::test(UsersTable::class)->call('deleteUser', $user->id);

    $this->assertSoftDeleted($user);
});

test('deleteSelected soft deletes all selected users', function () {
    $users = User::factory()->count(3)->create();

    Livewire::test(UsersTable::class)
        ->set('selected', $users->pluck('id')->toArray())
        ->call('deleteSelected');

    $users->each(fn (User $user) => $this->assertSoftDeleted($user));
});

/**
 * A plain Admin (even the shared, public-demo one) must never be able to
 * touch another Admin's account, or their own — only a Super Admin may
 * create/delete/(de)activate Admin accounts or grant/revoke the Admin role.
 */
function actingAsSuperAdmin(): User
{
    $superAdmin = User::factory()->create(['is_super_admin' => true]);
    $superAdmin->assignRole('Admin');
    test()->actingAs($superAdmin);

    return $superAdmin;
}

test('toggleActive refuses to deactivate your own account, even as a Super Admin', function () {
    $superAdmin = actingAsSuperAdmin();

    Livewire::test(UsersTable::class)->call('toggleActive', $superAdmin->id);

    expect($superAdmin->refresh()->is_active)->toBeTrue();
});

test('toggleActive refuses to deactivate another Admin when done by a plain Admin', function () {
    $targetAdmin = User::role('Admin')->firstOrFail(); // the admin from beforeEach's actingAsAdmin()
    $plainAdmin = User::factory()->create();
    $plainAdmin->assignRole('Admin');
    $this->actingAs($plainAdmin);

    Livewire::test(UsersTable::class)->call('toggleActive', $targetAdmin->id);

    expect($targetAdmin->refresh()->is_active)->toBeTrue();
});

test('toggleActive allows a Super Admin to deactivate another Admin', function () {
    $targetAdmin = User::role('Admin')->firstOrFail();
    actingAsSuperAdmin();

    Livewire::test(UsersTable::class)->call('toggleActive', $targetAdmin->id);

    expect($targetAdmin->refresh()->is_active)->toBeFalse();
});

test('deleteUser refuses to delete your own account, even as a Super Admin', function () {
    $superAdmin = actingAsSuperAdmin();

    Livewire::test(UsersTable::class)->call('deleteUser', $superAdmin->id);

    expect(User::find($superAdmin->id))->not->toBeNull();
});

test('deleteUser refuses to delete another Admin when done by a plain Admin', function () {
    $targetAdmin = User::role('Admin')->firstOrFail();
    $plainAdmin = User::factory()->create();
    $plainAdmin->assignRole('Admin');
    $this->actingAs($plainAdmin);

    Livewire::test(UsersTable::class)->call('deleteUser', $targetAdmin->id);

    expect(User::find($targetAdmin->id))->not->toBeNull();
});

test('deleteUser allows a Super Admin to delete another Admin', function () {
    $targetAdmin = User::role('Admin')->firstOrFail();
    actingAsSuperAdmin();

    Livewire::test(UsersTable::class)->call('deleteUser', $targetAdmin->id);

    $this->assertSoftDeleted($targetAdmin);
});

test('deleteUser still allows a plain Admin to delete a regular (non-Admin) user', function () {
    $regularUser = User::factory()->create();

    Livewire::test(UsersTable::class)->call('deleteUser', $regularUser->id);

    $this->assertSoftDeleted($regularUser);
});

test('deleteSelected refuses a selection that includes your own account', function () {
    $admin = auth()->user();
    $other = User::factory()->create();

    Livewire::test(UsersTable::class)
        ->set('selected', [$admin->id, $other->id])
        ->call('deleteSelected');

    expect(User::find($admin->id))->not->toBeNull()
        ->and(User::find($other->id))->not->toBeNull();
});

test('deleteSelected refuses a selection containing an Admin when done by a plain Admin', function () {
    $targetAdmin = User::role('Admin')->firstOrFail();
    $regularUser = User::factory()->create();
    $plainAdmin = User::factory()->create();
    $plainAdmin->assignRole('Admin');
    $this->actingAs($plainAdmin);

    Livewire::test(UsersTable::class)
        ->set('selected', [$targetAdmin->id, $regularUser->id])
        ->call('deleteSelected');

    expect(User::find($targetAdmin->id))->not->toBeNull()
        ->and(User::find($regularUser->id))->not->toBeNull();
});

test('deleteSelected allows a Super Admin to delete a selection containing an Admin', function () {
    $targetAdmin = User::role('Admin')->firstOrFail();
    actingAsSuperAdmin();

    Livewire::test(UsersTable::class)
        ->set('selected', [$targetAdmin->id])
        ->call('deleteSelected');

    $this->assertSoftDeleted($targetAdmin);
});

test('restoreUser restores a soft deleted regular user', function () {
    $user = User::factory()->create();
    $user->delete();

    Livewire::test(UsersTable::class)->call('restoreUser', $user->id);

    expect($user->fresh()->trashed())->toBeFalse();
});

test('restoreUser refuses to restore a soft deleted Admin when done by a plain Admin', function () {
    $targetAdmin = User::factory()->create();
    $targetAdmin->assignRole('Admin');
    $targetAdmin->delete();

    $plainAdmin = User::factory()->create();
    $plainAdmin->assignRole('Admin');
    $this->actingAs($plainAdmin);

    Livewire::test(UsersTable::class)->call('restoreUser', $targetAdmin->id);

    expect($targetAdmin->fresh()->trashed())->toBeTrue();
});

test('restoreUser allows a Super Admin to restore a soft deleted Admin', function () {
    $targetAdmin = User::factory()->create();
    $targetAdmin->assignRole('Admin');
    $targetAdmin->delete();

    actingAsSuperAdmin();

    Livewire::test(UsersTable::class)->call('restoreUser', $targetAdmin->id);

    expect($targetAdmin->fresh()->trashed())->toBeFalse();
});

test('forceDeleteUser permanently deletes a soft deleted regular user', function () {
    $user = User::factory()->create();
    $user->delete();

    Livewire::test(UsersTable::class)->call('forceDeleteUser', $user->id);

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

test('forceDeleteUser refuses to delete your own account, even as a Super Admin', function () {
    $superAdmin = actingAsSuperAdmin();
    $superAdmin->delete();

    Livewire::test(UsersTable::class)->call('forceDeleteUser', $superAdmin->id);

    $this->assertDatabaseHas('users', ['id' => $superAdmin->id]);
});

test('forceDeleteUser refuses to delete another Admin when done by a plain Admin', function () {
    $targetAdmin = User::role('Admin')->firstOrFail();
    $targetAdmin->delete();

    $plainAdmin = User::factory()->create();
    $plainAdmin->assignRole('Admin');
    $this->actingAs($plainAdmin);

    Livewire::test(UsersTable::class)->call('forceDeleteUser', $targetAdmin->id);

    $this->assertDatabaseHas('users', ['id' => $targetAdmin->id]);
});

test('forceDeleteUser allows a Super Admin to permanently delete another Admin', function () {
    $targetAdmin = User::role('Admin')->firstOrFail();
    $targetAdmin->delete();

    actingAsSuperAdmin();

    Livewire::test(UsersTable::class)->call('forceDeleteUser', $targetAdmin->id);

    $this->assertDatabaseMissing('users', ['id' => $targetAdmin->id]);
});
