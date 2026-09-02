<?php

use App\Livewire\Forms\ContractorsForm;
use App\Livewire\Forms\DriversForm;
use App\Models\Contractor;
use App\Models\Driver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

/**
 * WithDemoLimits — thresholds come from config/demo.php, lowered per-test here so
 * the tests stay fast and don't depend on the real defaults.
 * Driven mainly through DriversForm since it's the one form that exercises all
 * three limits (record count, rate limit, disk space); ContractorsForm is used
 * once to confirm the trait isn't just working by coincidence on Drivers.
 */
beforeEach(function () {
    actingAsAdmin();
});

function demoDriverPayload(): array
{
    return [
        'driverData.name' => 'Jan Kowalski',
        'driverData.phone' => '123456789',
        'driverData.pesel' => fake()->unique()->numerify('###########'),
        'driverData.driving_license_number' => fake()->unique()->bothify('???######'),
        'driverData.driving_license_expiry_date' => now()->addYear()->toDateString(),
        'driverData.identity_card_expiry_date' => now()->addYears(2)->toDateString(),
    ];
}

test('record creation is blocked once the demo max record count is reached', function () {
    config(['demo.max_records' => 2]);
    Driver::factory()->count(2)->create();

    Livewire::test(DriversForm::class)
        ->set(demoDriverPayload())
        ->call('save')
        ->assertHasErrors('demoLimit');

    expect(Driver::count())->toBe(2);
});

test('record creation is rate limited after too many attempts from the same IP', function () {
    config(['demo.record_creation.max_attempts' => 2, 'demo.max_records' => 100]);

    Livewire::test(DriversForm::class)->set(demoDriverPayload())->call('save')->assertHasNoErrors();
    Livewire::test(DriversForm::class)->set(demoDriverPayload())->call('save')->assertHasNoErrors();

    // Third attempt within the decay window, same IP — over the limit.
    Livewire::test(DriversForm::class)
        ->set(demoDriverPayload())
        ->call('save')
        ->assertHasErrors('demoLimit');

    expect(Driver::count())->toBe(2);
});

test('editing an existing record is not subject to demo record limits', function () {
    config(['demo.max_records' => 1, 'demo.record_creation.max_attempts' => 1]);
    // ->fresh(): Driver has no cast for the date columns, so right after
    // factory()->create() they're still whatever raw type the factory produced
    // in memory (a plain DateTime, not a string) — ->fresh() re-reads them from
    // the DB as strings, which is what the edit form's date-picker expects.
    $driver = Driver::factory()->create(['name' => 'Old name'])->fresh();

    // Both limits are already maxed out by the record above, but editing must
    // still go through — WithDemoLimits is only wired into the create branch.
    Livewire::test(DriversForm::class, ['driver' => $driver])
        ->set(demoDriverPayload())
        ->set('driverData.name', 'New name')
        ->call('save')
        ->assertHasNoErrors('demoLimit')
        ->assertRedirect(route('drivers.index'));

    expect($driver->refresh()->name)->toBe('New name');
});

test('uploading a document is blocked once the demo disk space limit is reached', function () {
    Storage::fake('driver_documents');
    config(['demo.max_disk_bytes' => 1]);

    Livewire::test(DriversForm::class)
        ->set(demoDriverPayload())
        ->set('driverData.driving_license_document_front', UploadedFile::fake()->image('license-front.jpg'))
        ->call('save')
        ->assertHasErrors('demoLimit');

    // The disk check runs before the driver row is written, precisely so a
    // rejected upload doesn't leave a document-less driver behind.
    expect(Driver::count())->toBe(0);
});

test('the record count limit also applies to a different form (contractors), not just drivers', function () {
    config(['demo.max_records' => 1]);
    Contractor::factory()->create();

    Livewire::test(ContractorsForm::class)
        ->set(['contractorData.name' => 'Nowy kontrahent'])
        ->call('save')
        ->assertHasErrors('demoLimit');

    expect(Contractor::count())->toBe(1);
});
