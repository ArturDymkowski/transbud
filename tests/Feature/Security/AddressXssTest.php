<?php

use App\Enums\CountriesEnum;
use App\Livewire\Tables\ContractorAddressesTable;
use App\Livewire\Tables\DeliveriesTable;
use App\Livewire\Tables\DriversTable;
use App\Models\Contractor;
use App\Models\ContractorAddress;
use App\Models\Delivery;
use App\Models\Driver;
use Livewire\Livewire;

/**
 * Regression tests for the stored-XSS fix in HasFullAddress (see ISSUES.md I6):
 * address fields used to be rendered with {!! !!} (raw HTML), so a <script> typed
 * into e.g. "street" would execute for anyone viewing the table. The trait now
 * exposes plain-text data (fullAddressLines/fullAddressText) and the views render
 * each line through {{ }}, so the tag must show up escaped, never literally.
 */
function maliciousStreetValue(): string
{
    return '<script>alert(1)</script>';
}

beforeEach(function () {
    actingAsAdmin();
});

test('a malicious street value is escaped, not executed, in the contractor addresses table', function () {
    $malicious = maliciousStreetValue();
    $contractor = Contractor::factory()->create();
    ContractorAddress::factory()->create([
        'contractor_id' => $contractor->id,
        'street' => $malicious,
    ]);

    Livewire::test(ContractorAddressesTable::class)
        ->assertDontSee($malicious, false)
        ->assertSee($malicious);
});

test('a malicious street value is escaped, not executed, in the drivers table', function () {
    $malicious = maliciousStreetValue();
    Driver::factory()->create(['street' => $malicious]);

    Livewire::test(DriversTable::class)
        ->assertDontSee($malicious, false)
        ->assertSee($malicious);
});

test('a malicious street value is escaped, not executed, in the deliveries table', function () {
    $malicious = maliciousStreetValue();
    $contractor = Contractor::factory()->create();
    $address = ContractorAddress::factory()->create([
        'contractor_id' => $contractor->id,
        'street' => $malicious,
    ]);
    Delivery::factory()->create([
        'contractor_id' => $contractor->id,
        'contractor_address_id' => $address->id,
    ]);

    Livewire::test(DeliveriesTable::class)
        ->assertDontSee($malicious, false)
        ->assertSee($malicious);
});

test('fullAddressLines splits the address into a street line and a city/country line', function () {
    $address = ContractorAddress::factory()->create([
        'country' => CountriesEnum::POLAND,
        'zipcode' => '00-001',
        'city' => 'Warszawa',
        'street' => 'Marszałkowska',
        'house_nr' => '10',
        'apartment_nr' => '5',
    ]);

    expect($address->fullAddressLines)->toBe([
        'Marszałkowska 10/5',
        '00-001 Warszawa, '.CountriesEnum::POLAND->label(),
    ]);
});

test('fullAddressText joins the lines into a single comma-separated string', function () {
    $address = ContractorAddress::factory()->create([
        'country' => CountriesEnum::POLAND,
        'zipcode' => '00-001',
        'city' => 'Warszawa',
        'street' => 'Marszałkowska',
        'house_nr' => '10',
        'apartment_nr' => null,
    ]);

    expect($address->fullAddressText)
        ->toBe('Marszałkowska 10, 00-001 Warszawa, '.CountriesEnum::POLAND->label());
});

test('fullAddressLines is empty and fullAddressText falls back to "-" when nothing is set', function () {
    $driver = Driver::factory()->create([
        'country' => null,
        'zipcode' => null,
        'city' => null,
        'street' => null,
        'house_nr' => null,
        'apartment_nr' => null,
    ]);

    expect($driver->fullAddressLines)->toBe([]);
    expect($driver->fullAddressText)->toBe('-');
});
