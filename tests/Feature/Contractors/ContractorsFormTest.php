<?php

use App\Livewire\Forms\ContractorsForm;
use App\Models\Contractor;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

function validContractorPayload(): array
{
    return [
        'contractorData.name' => 'Acme Sp. z o.o.',
        'contractorData.nip' => '1234567890',
        'contractorData.regon' => '123456789',
        'contractorData.phone' => '123456789',
        'contractorData.email' => 'kontakt@acme.pl',
        'contractorData.notes' => 'Stały klient.',
    ];
}

test('contractor create page renders the form', function () {
    $this->get(route('contractors.create'))->assertOk();
});

test('contractor edit page renders the form', function () {
    $contractor = Contractor::factory()->create();

    $this->get(route('contractors.edit', $contractor))->assertOk();
});

test('required fields are validated on create', function () {
    Livewire::test(ContractorsForm::class)
        ->set('contractorData.name', '')
        ->call('save')
        ->assertHasErrors(['contractorData.name' => 'required']);

    $this->assertDatabaseCount('contractors', 0);
});

test('email must be a valid address', function () {
    Livewire::test(ContractorsForm::class)
        ->set(validContractorPayload())
        ->set('contractorData.email', 'not-an-email')
        ->call('save')
        ->assertHasErrors(['contractorData.email' => 'email']);
});

test('a new contractor can be created with valid data and defaults to active', function () {
    Livewire::test(ContractorsForm::class)
        ->set(validContractorPayload())
        ->call('save')
        ->assertRedirect(route('contractors.index'));

    $contractor = Contractor::where('nip', '1234567890')->firstOrFail();

    expect($contractor->active)->toBeTrue();
    expect(session('success'))->toBe(trans('labels.general.saved_success'));
});

test('an existing contractor can be edited', function () {
    $contractor = Contractor::factory()->create(['name' => 'Old Name'])->fresh();

    Livewire::test(ContractorsForm::class, ['contractor' => $contractor])
        ->set('contractorData.name', 'New Name')
        ->call('save')
        ->assertRedirect(route('contractors.index'));

    expect($contractor->refresh()->name)->toBe('New Name');
    expect(session('success'))->toBe(trans('labels.general.updated_success'));
});

test('active can be unchecked when editing a contractor', function () {
    $contractor = Contractor::factory()->create(['active' => true])->fresh();

    Livewire::test(ContractorsForm::class, ['contractor' => $contractor])
        ->set('contractorData.active', false)
        ->call('save')
        ->assertRedirect(route('contractors.index'));

    expect($contractor->refresh()->active)->toBeFalse();
});
