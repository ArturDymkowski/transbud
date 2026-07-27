<?php

use App\Livewire\Forms\UsersForm;
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
