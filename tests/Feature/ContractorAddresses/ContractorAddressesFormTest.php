<?php

use App\Enums\CountriesEnum;
use App\Livewire\Forms\ContractorAddressesForm;
use App\Models\Contractor;
use App\Models\ContractorAddress;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

function validContractorAddressPayload(int $contractorId): array
{
    return [
        'addressData.contractor_id' => $contractorId,
        'addressData.country' => CountriesEnum::POLAND->value,
        'addressData.zipcode' => '00-001',
        'addressData.city' => 'Warszawa',
        'addressData.street' => 'Marszałkowska',
        'addressData.house_nr' => '1',
        'addressData.apartment_nr' => '2',
    ];
}

test('address create page renders the form', function () {
    $this->get(route('contractor-addresses.create'))->assertOk();
});

test('address edit page renders the form', function () {
    $address = ContractorAddress::factory()->create();

    $this->get(route('contractor-addresses.edit', $address))->assertOk();
});

test('required fields are validated on create', function () {
    Livewire::test(ContractorAddressesForm::class)
        ->set('addressData.contractor_id', '')
        ->set('addressData.country', '')
        ->set('addressData.zipcode', '')
        ->set('addressData.city', '')
        ->set('addressData.street', '')
        ->call('save')
        ->assertHasErrors([
            'addressData.contractor_id' => 'required',
            'addressData.country' => 'required',
            'addressData.zipcode' => 'required',
            'addressData.city' => 'required',
            'addressData.street' => 'required',
        ]);

    $this->assertDatabaseCount('contractor_addresses', 0);
});

test('contractor must exist', function () {
    Livewire::test(ContractorAddressesForm::class)
        ->set(validContractorAddressPayload(999))
        ->call('save')
        ->assertHasErrors(['addressData.contractor_id' => 'exists']);
});

test('a new address can be created with valid data', function () {
    $contractor = Contractor::factory()->create();

    Livewire::test(ContractorAddressesForm::class)
        ->set(validContractorAddressPayload($contractor->id))
        ->call('save')
        ->assertRedirect(route('contractor-addresses.index'));

    $this->assertDatabaseHas('contractor_addresses', [
        'contractor_id' => $contractor->id,
        'city' => 'Warszawa',
        'street' => 'Marszałkowska',
    ]);

    expect(session('success'))->toBe(trans('labels.general.saved_success'));
});

test('an existing address can be edited', function () {
    $address = ContractorAddress::factory()->create(['city' => 'Old City'])->fresh();

    Livewire::test(ContractorAddressesForm::class, ['contractorAddress' => $address])
        ->set('addressData.city', 'New City')
        ->call('save')
        ->assertRedirect(route('contractor-addresses.index'));

    expect($address->refresh()->city)->toBe('New City');
    expect(session('success'))->toBe(trans('labels.general.updated_success'));
});

test('editing an address can reassign it to a different contractor', function () {
    $originalContractor = Contractor::factory()->create();
    $newContractor = Contractor::factory()->create();
    $address = ContractorAddress::factory()->create(['contractor_id' => $originalContractor->id])->fresh();

    Livewire::test(ContractorAddressesForm::class, ['contractorAddress' => $address])
        ->set('addressData.contractor_id', $newContractor->id)
        ->call('save')
        ->assertRedirect(route('contractor-addresses.index'));

    expect($address->refresh()->contractor_id)->toBe($newContractor->id);
});
