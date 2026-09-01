<?php

use App\Livewire\Forms\UsersForm;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

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

test('an existing user password can be changed', function () {
    $user = User::factory()->create()->fresh();

    Livewire::test(UsersForm::class, ['user' => $user])
        ->set('userData.password', 'newpassword123')
        ->set('userData.password_confirmation', 'newpassword123')
        ->call('save')
        ->assertRedirect(route('users.index'));

    expect(Hash::check('newpassword123', $user->refresh()->password))->toBeTrue();
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
    $self->assignRole('Admin');
    $this->actingAs($self);

    Livewire::test(UsersForm::class, ['user' => $self])
        ->set('userData.role_id', $roleB->id)
        ->assertSet('isEditingSelf', true)
        ->call('save')
        ->assertRedirect(route('users.index'));

    expect($self->refresh()->roles->pluck('name')->all())->toBe(['Admin']);
});

test('the role field is disabled in the form when editing your own account', function () {
    $self = User::factory()->create()->fresh();
    $self->assignRole('Admin');
    $this->actingAs($self);

    $html = Livewire::test(UsersForm::class, ['user' => $self])->html();

    expect($html)->toContain('disabled');
});
