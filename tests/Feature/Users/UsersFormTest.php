<?php

use App\Enums\RoleEnum;
use App\Livewire\Forms\UsersForm;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

beforeEach(function () {
    actingAsAdmin();
});

function validUserPayload(): array
{
    return [
        'userData.name' => 'Jan Kowalski',
        'userData.email' => 'jan.kowalski@example.com',
        'userData.password' => 'password123',
        'userData.password_confirmation' => 'password123',
    ];
}

test('required fields are validated on create', function () {
    Livewire::test(UsersForm::class)
        ->set('userData.name', '')
        ->set('userData.email', '')
        ->set('userData.password', '')
        ->call('save')
        ->assertHasErrors([
            'userData.name' => 'required',
            'userData.email' => 'required',
            'userData.password' => 'required',
        ]);

    $this->assertDatabaseCount('users', 1);
});

test('email must be a valid email', function () {
    Livewire::test(UsersForm::class)
        ->set(validUserPayload())
        ->set('userData.email', 'not-an-email')
        ->call('save')
        ->assertHasErrors(['userData.email' => 'email']);
});

test('email must be unique among users', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    Livewire::test(UsersForm::class)
        ->set(validUserPayload())
        ->set('userData.email', 'taken@example.com')
        ->call('save')
        ->assertHasErrors(['userData.email' => 'unique']);
});

test('password must be confirmed', function () {
    Livewire::test(UsersForm::class)
        ->set(validUserPayload())
        ->set('userData.password_confirmation', 'different')
        ->call('save')
        ->assertHasErrors(['userData.password' => 'confirmed']);
});

test('password must be at least 8 characters', function () {
    Livewire::test(UsersForm::class)
        ->set(validUserPayload())
        ->set('userData.password', 'short')
        ->set('userData.password_confirmation', 'short')
        ->call('save')
        ->assertHasErrors(['userData.password' => 'min']);
});

test('a new user can be created with valid data', function () {
    Livewire::test(UsersForm::class)
        ->set(validUserPayload())
        ->call('save')
        ->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'name' => 'Jan Kowalski',
        'email' => 'jan.kowalski@example.com',
    ]);

    $user = User::where('email', 'jan.kowalski@example.com')->first();
    expect(Hash::check('password123', $user->password))->toBeTrue();

    expect(session('success'))->toBe(trans('labels.general.saved_success'));
});

test('an existing user can be edited without changing the password', function () {
    $user = User::factory()->create(['name' => 'Old name', 'password' => Hash::make('original-password')])->fresh();
    $originalHash = $user->password;

    Livewire::test(UsersForm::class, ['user' => $user])
        ->set('userData.name', 'New name')
        ->call('save')
        ->assertRedirect(route('users.index'));

    expect($user->refresh()->name)->toBe('New name');
    expect($user->password)->toBe($originalHash);
    expect(session('success'))->toBe(trans('labels.general.updated_success'));
});

test('the password field is only present on the create form', function () {
    $createHtml = Livewire::test(UsersForm::class)->html();
    expect($createHtml)->toContain('userData.password');

    $user = User::factory()->create()->fresh();
    $editHtml = Livewire::test(UsersForm::class, ['user' => $user])->html();
    expect($editHtml)->not->toContain('userData.password');
});

test('a password sent to an existing user is ignored, since the edit form has no password field', function () {
    $user = User::factory()->create(['password' => Hash::make('original-password')])->fresh();
    $originalHash = $user->password;

    Livewire::test(UsersForm::class, ['user' => $user])
        ->set('userData.password', 'newpassword123')
        ->set('userData.password_confirmation', 'newpassword123')
        ->call('save')
        ->assertRedirect(route('users.index'));

    expect($user->refresh()->password)->toBe($originalHash);
});

test('editing a user keeps its own email valid despite the uniqueness rule', function () {
    $user = User::factory()->create(['email' => 'jan.kowalski@example.com'])->fresh();

    Livewire::test(UsersForm::class, ['user' => $user])
        ->set('userData.name', 'Updated name')
        ->call('save')
        ->assertHasNoErrors(['userData.email'])
        ->assertRedirect(route('users.index'));
});

test('a role can be assigned to a user on create', function () {
    $role = Role::create(['name' => 'Dispatcher']);

    Livewire::test(UsersForm::class)
        ->set(validUserPayload())
        ->set('userData.role_id', $role->id)
        ->call('save')
        ->assertRedirect(route('users.index'));

    $user = User::where('email', 'jan.kowalski@example.com')->first();
    expect($user->hasRole('Dispatcher'))->toBeTrue();
});

test('a plain Admin can create a new user with the Admin role', function () {
    $adminRole = Role::where('name', RoleEnum::ADMIN->value)->firstOrFail();

    Livewire::test(UsersForm::class)
        ->set(validUserPayload())
        ->set('userData.role_id', $adminRole->id)
        ->call('save')
        ->assertRedirect(route('users.index'));

    $user = User::where('email', 'jan.kowalski@example.com')->first();
    expect($user->hasRole(RoleEnum::ADMIN->value))->toBeTrue();
});

test('a role can be assigned when its id arrives as a string, as the select input sends it', function () {
    $role = Role::create(['name' => 'Dispatcher']);

    Livewire::test(UsersForm::class)
        ->set(validUserPayload())
        ->set('userData.role_id', (string) $role->id)
        ->call('save')
        ->assertRedirect(route('users.index'));

    $user = User::where('email', 'jan.kowalski@example.com')->first();
    expect($user->hasRole('Dispatcher'))->toBeTrue();
});

test('a user can have at most one role, switching replaces the previous one', function () {
    $roleA = Role::create(['name' => 'Dispatcher']);
    $roleB = Role::create(['name' => 'Accountant']);

    $user = User::factory()->create()->fresh();
    $user->assignRole($roleA);

    Livewire::test(UsersForm::class, ['user' => $user])
        ->set('userData.role_id', $roleB->id)
        ->call('save')
        ->assertRedirect(route('users.index'));

    expect($user->refresh()->roles->pluck('name')->all())->toBe(['Accountant']);
});

test('a role can be unassigned from a user by selecting no role', function () {
    $role = Role::create(['name' => 'Dispatcher']);

    $user = User::factory()->create()->fresh();
    $user->assignRole($role);

    Livewire::test(UsersForm::class, ['user' => $user])
        ->set('userData.role_id', '')
        ->call('save')
        ->assertRedirect(route('users.index'));

    expect($user->refresh()->roles)->toBeEmpty();
});

test('is_super_admin cannot be set through the form, even if injected into the payload', function () {
    Livewire::test(UsersForm::class)
        ->set(validUserPayload())
        ->set('userData.is_super_admin', true)
        ->call('save')
        ->assertRedirect(route('users.index'));

    $user = User::where('email', 'jan.kowalski@example.com')->first();
    expect($user->is_super_admin)->toBeFalse();
});

test('a user editing their own account cannot change their own role, even with users.edit permission', function () {
    $roleB = Role::create(['name' => 'Accountant']);

    $self = User::factory()->create()->fresh();
    $self->assignRole(RoleEnum::ADMIN->value);
    $this->actingAs($self);

    Livewire::test(UsersForm::class, ['user' => $self])
        ->set('userData.role_id', $roleB->id)
        ->assertSet('isEditingSelf', true)
        ->call('save')
        ->assertRedirect(route('users.index'));

    expect($self->refresh()->roles->pluck('name')->all())->toBe([RoleEnum::ADMIN->value]);
});

/**
 * Scoped to the actual <select> tag (matched by its id) rather than a plain
 * `toContain('disabled')` — the page also has Tailwind `disabled:...` variant
 * classes elsewhere (e.g. on buttons), which would make a bare substring check
 * pass regardless of whether the field itself is really disabled.
 */
function roleSelectTag(string $html): string
{
    preg_match('/<select[^>]*id="userData\.role_id"[^>]*>/s', $html, $matches);

    return $matches[0] ?? '';
}

test('the role field is disabled in the form when editing your own account', function () {
    $self = User::factory()->create()->fresh();
    $self->assignRole(RoleEnum::ADMIN->value);
    $this->actingAs($self);

    $html = Livewire::test(UsersForm::class, ['user' => $self])->html();
    $selectTag = roleSelectTag($html);

    expect($selectTag)->not->toBe('');
    expect($selectTag)->toContain('disabled');
});

test('the role field is not disabled when editing a different account', function () {
    $other = User::factory()->create();

    $html = Livewire::test(UsersForm::class, ['user' => $other])->html();
    $selectTag = roleSelectTag($html);

    expect($selectTag)->not->toBe('');
    expect($selectTag)->not->toContain('disabled');
});

test('a plain Admin cannot change another Admin\'s role', function () {
    $targetAdmin = User::role(RoleEnum::ADMIN->value)->firstOrFail();

    $plainAdmin = User::factory()->create();
    $plainAdmin->assignRole(RoleEnum::ADMIN->value);
    $this->actingAs($plainAdmin);

    $dispatcherRole = Role::create(['name' => 'Dispatcher']);

    Livewire::test(UsersForm::class, ['user' => $targetAdmin])
        ->set('userData.role_id', $dispatcherRole->id)
        ->call('save')
        ->assertHasNoErrors('userData.role_id')
        ->assertRedirect(route('users.index'));

    expect($targetAdmin->refresh()->hasRole(RoleEnum::ADMIN->value))->toBeTrue();
});

test('the role field is read-only when a plain Admin edits another Admin', function () {
    $targetAdmin = User::role(RoleEnum::ADMIN->value)->firstOrFail();

    $plainAdmin = User::factory()->create();
    $plainAdmin->assignRole(RoleEnum::ADMIN->value);
    $this->actingAs($plainAdmin);

    $html = Livewire::test(UsersForm::class, ['user' => $targetAdmin])->html();

    expect(roleSelectTag($html))->toContain('disabled');
});

test('a plain Admin can promote another user to the Admin role', function () {
    $regularUser = User::factory()->create();
    $adminRole = Role::where('name', RoleEnum::ADMIN->value)->firstOrFail();

    Livewire::test(UsersForm::class, ['user' => $regularUser])
        ->set('userData.role_id', $adminRole->id)
        ->call('save')
        ->assertHasNoErrors('userData.role_id')
        ->assertRedirect(route('users.index'));

    expect($regularUser->refresh()->hasRole(RoleEnum::ADMIN->value))->toBeTrue();
});

test('a Super Admin can demote another Admin away from the Admin role', function () {
    $targetAdmin = User::role(RoleEnum::ADMIN->value)->firstOrFail();

    $superAdmin = User::factory()->create(['is_super_admin' => true]);
    $superAdmin->assignRole(RoleEnum::ADMIN->value);
    $this->actingAs($superAdmin);

    $dispatcherRole = Role::create(['name' => 'Dispatcher']);

    Livewire::test(UsersForm::class, ['user' => $targetAdmin])
        ->set('userData.role_id', $dispatcherRole->id)
        ->call('save')
        ->assertHasNoErrors('userData.role_id')
        ->assertRedirect(route('users.index'));

    expect($targetAdmin->refresh()->hasRole('Dispatcher'))->toBeTrue();
});

test('a Super Admin can promote another user to the Admin role', function () {
    $regularUser = User::factory()->create();
    $adminRole = Role::where('name', RoleEnum::ADMIN->value)->firstOrFail();

    $superAdmin = User::factory()->create(['is_super_admin' => true]);
    $superAdmin->assignRole(RoleEnum::ADMIN->value);
    $this->actingAs($superAdmin);

    Livewire::test(UsersForm::class, ['user' => $regularUser])
        ->set('userData.role_id', $adminRole->id)
        ->call('save')
        ->assertHasNoErrors('userData.role_id')
        ->assertRedirect(route('users.index'));

    expect($regularUser->refresh()->hasRole(RoleEnum::ADMIN->value))->toBeTrue();
});

test('a plain Admin can still assign a non-Admin role to a regular user', function () {
    $regularUser = User::factory()->create();
    $dispatcherRole = Role::create(['name' => 'Dispatcher']);

    Livewire::test(UsersForm::class, ['user' => $regularUser])
        ->set('userData.role_id', $dispatcherRole->id)
        ->call('save')
        ->assertHasNoErrors('userData.role_id')
        ->assertRedirect(route('users.index'));

    expect($regularUser->refresh()->hasRole('Dispatcher'))->toBeTrue();
});

function nameInputTag(string $html): string
{
    preg_match('/<input[^>]*id="userData\.name"[^>]*>/s', $html, $matches);

    return $matches[0] ?? '';
}

function emailInputTag(string $html): string
{
    preg_match('/<input[^>]*id="userData\.email"[^>]*>/s', $html, $matches);

    return $matches[0] ?? '';
}

test('the name and email fields are read-only when a plain Admin edits another Admin', function () {
    $targetAdmin = User::role(RoleEnum::ADMIN->value)->firstOrFail();

    $plainAdmin = User::factory()->create();
    $plainAdmin->assignRole(RoleEnum::ADMIN->value);
    $this->actingAs($plainAdmin);

    $html = Livewire::test(UsersForm::class, ['user' => $targetAdmin])->html();

    expect(nameInputTag($html))->toContain('disabled');
    expect(emailInputTag($html))->toContain('disabled');
});

test('a plain Admin cannot change another Admin\'s name or email, even if submitted directly', function () {
    $targetAdmin = User::role(RoleEnum::ADMIN->value)->firstOrFail();
    $originalName = $targetAdmin->name;
    $originalEmail = $targetAdmin->email;

    $plainAdmin = User::factory()->create();
    $plainAdmin->assignRole(RoleEnum::ADMIN->value);
    $this->actingAs($plainAdmin);

    Livewire::test(UsersForm::class, ['user' => $targetAdmin])
        ->set('userData.name', 'Tampered name')
        ->set('userData.email', 'tampered@example.com')
        ->call('save')
        ->assertRedirect(route('users.index'));

    expect($targetAdmin->refresh()->name)->toBe($originalName);
    expect($targetAdmin->refresh()->email)->toBe($originalEmail);
});

test('the name and email fields are not read-only when a Super Admin edits another Admin', function () {
    $targetAdmin = User::role(RoleEnum::ADMIN->value)->firstOrFail();

    $superAdmin = User::factory()->create(['is_super_admin' => true]);
    $this->actingAs($superAdmin);

    $html = Livewire::test(UsersForm::class, ['user' => $targetAdmin])->html();

    expect(nameInputTag($html))->not->toContain('disabled');
    expect(emailInputTag($html))->not->toContain('disabled');
});

test('the name and email fields are not read-only when a plain Admin edits a non-Admin user', function () {
    $regularUser = User::factory()->create();

    $plainAdmin = User::factory()->create();
    $plainAdmin->assignRole(RoleEnum::ADMIN->value);
    $this->actingAs($plainAdmin);

    $html = Livewire::test(UsersForm::class, ['user' => $regularUser])->html();

    expect(nameInputTag($html))->not->toContain('disabled');
    expect(emailInputTag($html))->not->toContain('disabled');
});

test('an Admin editing their own name and email is not blocked, even though they hold the Admin role', function () {
    $self = User::factory()->create()->fresh();
    $self->assignRole(RoleEnum::ADMIN->value);
    $this->actingAs($self);

    $html = Livewire::test(UsersForm::class, ['user' => $self])->html();

    expect(nameInputTag($html))->not->toContain('disabled');
    expect(emailInputTag($html))->not->toContain('disabled');
});
