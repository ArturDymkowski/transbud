<?php

use App\Enums\CountriesEnum;
use App\Livewire\Tables\ContractorAddressesTable;
use App\Models\Contractor;
use App\Models\ContractorAddress;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('guest is redirected from the address book', function () {
    auth()->logout();

    $this->get(route('contractor-addresses.index'))->assertRedirect(route('login'));
});

test('address book index page lists addresses', function () {
    $contractor = Contractor::factory()->create(['name' => 'Acme Sp. z o.o.']);
    ContractorAddress::factory()->create(['contractor_id' => $contractor->id, 'city' => 'Warszawa']);

    $this->get(route('contractor-addresses.index'))
        ->assertOk()
        ->assertSee('Acme Sp. z o.o.')
        ->assertSee('Warszawa');
});

test('search filters addresses by contractor name', function () {
    $acme = Contractor::factory()->create(['name' => 'Acme Sp. z o.o.']);
    $globex = Contractor::factory()->create(['name' => 'Globex S.A.']);
    ContractorAddress::factory()->create(['contractor_id' => $acme->id]);
    ContractorAddress::factory()->create(['contractor_id' => $globex->id]);

    Livewire::test(ContractorAddressesTable::class)
        ->set('search', 'Acme')
        ->assertSee('Acme Sp. z o.o.')
        ->assertDontSee('Globex S.A.');
});

test('search filters addresses by city', function () {
    $contractor = Contractor::factory()->create();
    ContractorAddress::factory()->create(['contractor_id' => $contractor->id, 'city' => 'Warszawa', 'street' => 'Marszałkowska']);
    ContractorAddress::factory()->create(['contractor_id' => $contractor->id, 'city' => 'Kraków', 'street' => 'Floriańska']);

    Livewire::test(ContractorAddressesTable::class)
        ->set('search', 'Warszawa')
        ->assertSee('Marszałkowska')
        ->assertDontSee('Floriańska');
});

test('active filter narrows the list to active or inactive addresses', function () {
    $contractor = Contractor::factory()->create();
    ContractorAddress::factory()->create(['contractor_id' => $contractor->id, 'street' => 'Active Street', 'is_active' => true]);
    ContractorAddress::factory()->create(['contractor_id' => $contractor->id, 'street' => 'Inactive Street', 'is_active' => false]);

    Livewire::test(ContractorAddressesTable::class)
        ->set('isActive', '1')
        ->assertSee('Active Street')
        ->assertDontSee('Inactive Street');
});

test('country filter narrows the list', function () {
    $contractor = Contractor::factory()->create();
    ContractorAddress::factory()->create(['contractor_id' => $contractor->id, 'country' => CountriesEnum::POLAND->value, 'street' => 'Marszałkowska']);
    ContractorAddress::factory()->create(['contractor_id' => $contractor->id, 'country' => CountriesEnum::GERMANY->value, 'street' => 'Floriańska']);

    Livewire::test(ContractorAddressesTable::class)
        ->set('country', CountriesEnum::POLAND->value)
        ->assertSee('Marszałkowska')
        ->assertDontSee('Floriańska');
});

test('sorting by contractor name orders addresses', function () {
    $zeta = Contractor::factory()->create(['name' => 'Zeta']);
    $alpha = Contractor::factory()->create(['name' => 'Alpha']);
    ContractorAddress::factory()->create(['contractor_id' => $zeta->id]);
    ContractorAddress::factory()->create(['contractor_id' => $alpha->id]);

    Livewire::test(ContractorAddressesTable::class)
        ->set('sortField', 'contractor_name')
        ->set('sortDirection', 'asc')
        ->assertSeeInOrder(['Alpha', 'Zeta']);
});

test('trashed filter can include soft deleted addresses', function () {
    $contractor = Contractor::factory()->create();
    $address = ContractorAddress::factory()->create(['contractor_id' => $contractor->id, 'city' => 'Deleted City']);
    $address->delete();

    Livewire::test(ContractorAddressesTable::class)
        ->assertDontSee('Deleted City')
        ->set('trashed', 'with')
        ->assertSee('Deleted City');
});

test('deleteSelected removes all selected addresses', function () {
    $contractor = Contractor::factory()->create();
    $addresses = ContractorAddress::factory()->count(3)->create(['contractor_id' => $contractor->id]);

    Livewire::test(ContractorAddressesTable::class)
        ->set('selected', $addresses->pluck('id')->toArray())
        ->call('deleteSelected');

    $addresses->each(fn (ContractorAddress $address) => $this->assertSoftDeleted($address));
});

test('deleteAddress removes a single address', function () {
    $address = ContractorAddress::factory()->create();

    Livewire::test(ContractorAddressesTable::class)->call('deleteAddress', $address->id);

    $this->assertSoftDeleted($address);
});

test('toggleActive flips the is_active flag', function () {
    $address = ContractorAddress::factory()->create(['is_active' => true]);

    Livewire::test(ContractorAddressesTable::class)->call('toggleActive', $address->id);

    expect($address->refresh()->is_active)->toBeFalse();
});

test('scoping to a contractor only shows that contractor\'s addresses', function () {
    $acme = Contractor::factory()->create(['name' => 'Acme Sp. z o.o.']);
    $globex = Contractor::factory()->create(['name' => 'Globex S.A.']);
    ContractorAddress::factory()->create(['contractor_id' => $acme->id, 'street' => 'Acme Street']);
    ContractorAddress::factory()->create(['contractor_id' => $globex->id, 'street' => 'Globex Street']);

    Livewire::test(ContractorAddressesTable::class, ['contractor' => $acme])
        ->assertSee('Acme Street')
        ->assertDontSee('Globex Street')
        ->assertDontSee('Kontrahent');
});

test('contractor edit page shows an address book tab scoped to that contractor', function () {
    $contractor = Contractor::factory()->create(['name' => 'Acme Sp. z o.o.']);
    ContractorAddress::factory()->create(['contractor_id' => $contractor->id, 'street' => 'Acme Street']);

    $this->get(route('contractors.edit', $contractor))
        ->assertOk()
        ->assertSee(__('address_book.plural_model_label'))
        ->assertSee('Acme Street');
});

test('createAddress requires the address fields when scoped to a contractor', function () {
    $contractor = Contractor::factory()->create();

    Livewire::test(ContractorAddressesTable::class, ['contractor' => $contractor])
        ->call('openCreateModal')
        ->set('createAddressData.country', '')
        ->set('createAddressData.zipcode', '')
        ->set('createAddressData.city', '')
        ->set('createAddressData.street', '')
        ->call('createAddress')
        ->assertHasErrors([
            'createAddressData.country' => 'required',
            'createAddressData.zipcode' => 'required',
            'createAddressData.city' => 'required',
            'createAddressData.street' => 'required',
        ]);

    $this->assertDatabaseCount('contractor_addresses', 0);
});

test('createAddress assigns the new address to the scoped contractor and closes the modal', function () {
    $contractor = Contractor::factory()->create();

    Livewire::test(ContractorAddressesTable::class, ['contractor' => $contractor])
        ->call('openCreateModal')
        ->set('createAddressData.country', CountriesEnum::POLAND->value)
        ->set('createAddressData.zipcode', '00-001')
        ->set('createAddressData.city', 'Warszawa')
        ->set('createAddressData.street', 'Marszałkowska')
        ->call('createAddress')
        ->assertSet('showCreateModal', false)
        ->assertSee('Marszałkowska');

    $this->assertDatabaseHas('contractor_addresses', [
        'contractor_id' => $contractor->id,
        'city' => 'Warszawa',
        'street' => 'Marszałkowska',
    ]);
});
