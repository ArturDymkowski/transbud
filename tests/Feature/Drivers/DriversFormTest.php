<?php

use App\Livewire\Forms\DriversForm;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

function validDriverPayload(): array
{
    return [
        'driverData.name' => 'Jan Kowalski',
        'driverData.phone' => '123456789',
        'driverData.pesel' => '12345678901',
        'driverData.driving_license_number' => 'ABC123456',
        'driverData.driving_license_expiry_date' => now()->addYear()->toDateString(),
        'driverData.identity_card_expiry_date' => now()->addYears(2)->toDateString(),
    ];
}

test('required fields are validated on create', function () {
    Livewire::test(DriversForm::class)
        ->set('driverData.name', '')
        ->set('driverData.phone', '')
        ->set('driverData.pesel', '')
        ->set('driverData.driving_license_number', '')
        ->set('driverData.driving_license_expiry_date', '')
        ->set('driverData.identity_card_expiry_date', '')
        ->call('save')
        ->assertHasErrors([
            'driverData.name' => 'required',
            'driverData.phone' => 'required',
            'driverData.pesel' => 'required',
            'driverData.driving_license_number' => 'required',
            'driverData.driving_license_expiry_date' => 'required',
            'driverData.identity_card_expiry_date' => 'required',
        ]);

    $this->assertDatabaseCount('drivers', 0);
});

test('pesel must be exactly 11 characters', function () {
    Livewire::test(DriversForm::class)
        ->set(validDriverPayload())
        ->set('driverData.pesel', '123')
        ->call('save')
        ->assertHasErrors(['driverData.pesel' => 'size']);
});

test('pesel must be unique among drivers', function () {
    Driver::factory()->create(['pesel' => '12345678901']);

    Livewire::test(DriversForm::class)
        ->set(validDriverPayload())
        ->call('save')
        ->assertHasErrors(['driverData.pesel' => 'unique']);
});

test('driving license number must be unique among drivers', function () {
    Driver::factory()->create(['driving_license_number' => 'ABC123456']);

    Livewire::test(DriversForm::class)
        ->set(validDriverPayload())
        ->set('driverData.pesel', '99999999999')
        ->call('save')
        ->assertHasErrors(['driverData.driving_license_number' => 'unique']);
});

test('a new driver can be created with valid data', function () {
    Livewire::test(DriversForm::class)
        ->set(validDriverPayload())
        ->call('save')
        ->assertRedirect(route('drivers.index'));

    $this->assertDatabaseHas('drivers', [
        'name' => 'Jan Kowalski',
        'pesel' => '12345678901',
        'driving_license_number' => 'ABC123456',
    ]);

    expect(session('success'))->toBe(trans('labels.general.saved_success'));
});

test('an existing driver can be edited', function () {
    // ->fresh() forces a DB round-trip so date columns come back as strings,
    // matching what route-model binding gives the real controller/component.
    $driver = Driver::factory()->create(['name' => 'Old Name'])->fresh();

    Livewire::test(DriversForm::class, ['driver' => $driver])
        ->set('driverData.name', 'New Name')
        ->call('save')
        ->assertRedirect(route('drivers.index'));

    expect($driver->refresh()->name)->toBe('New Name');
    expect(session('success'))->toBe(trans('labels.general.updated_success'));
});

test('editing a driver keeps its own pesel valid despite the uniqueness rule', function () {
    $driver = Driver::factory()->create(['pesel' => '11111111111'])->fresh();

    Livewire::test(DriversForm::class, ['driver' => $driver])
        ->set('driverData.name', 'Updated Name')
        ->set('driverData.pesel', '11111111111')
        ->call('save')
        ->assertHasNoErrors('driverData.pesel')
        ->assertRedirect(route('drivers.index'));
});

test('uploading a document attaches it to the correct media collection', function () {
    Storage::fake('driver_documents');

    Livewire::test(DriversForm::class)
        ->set(validDriverPayload())
        ->set('driverData.driving_license_document_front', UploadedFile::fake()->image('license-front.jpg'))
        ->call('save')
        ->assertRedirect(route('drivers.index'));

    $driver = Driver::where('pesel', '12345678901')->firstOrFail();

    expect($driver->getFirstMedia(Driver::MEDIA_DRIVING_LICENSE_FRONT))->not->toBeNull();
});

test('tractor and trailer fields are optional', function () {
    Livewire::test(DriversForm::class)
        ->set(validDriverPayload())
        ->call('save')
        ->assertHasNoErrors(['driverData.tractor_id', 'driverData.trailer_id'])
        ->assertRedirect(route('drivers.index'));

    $driver = Driver::where('pesel', '12345678901')->firstOrFail();
    expect($driver->vehicles)->toBeEmpty();
});

test('a driver can be assigned a tractor and a trailer', function () {
    $tractor = Vehicle::factory()->create(['type' => 0]);
    $trailer = Vehicle::factory()->create(['type' => 1]);

    Livewire::test(DriversForm::class)
        ->set(validDriverPayload())
        ->set('driverData.tractor_id', $tractor->id)
        ->set('driverData.trailer_id', $trailer->id)
        ->call('save')
        ->assertRedirect(route('drivers.index'));

    $driver = Driver::where('pesel', '12345678901')->firstOrFail();
    expect($driver->vehicles->pluck('id')->sort()->values()->all())
        ->toBe([$tractor->id, $trailer->id]);
});

test('a vehicle of the wrong type is rejected for tractor and trailer fields', function () {
    $trailer = Vehicle::factory()->create(['type' => 1]);
    $tractor = Vehicle::factory()->create(['type' => 0]);

    Livewire::test(DriversForm::class)
        ->set(validDriverPayload())
        ->set('driverData.tractor_id', $trailer->id)
        ->set('driverData.trailer_id', $tractor->id)
        ->call('save')
        ->assertHasErrors(['driverData.tractor_id' => 'exists', 'driverData.trailer_id' => 'exists']);
});

test('editing a driver can change and unassign its vehicles', function () {
    $tractor = Vehicle::factory()->create(['type' => 0]);
    $newTractor = Vehicle::factory()->create(['type' => 0]);
    $trailer = Vehicle::factory()->create(['type' => 1]);

    $driver = Driver::factory()->create()->fresh();
    $driver->vehicles()->sync([$tractor->id, $trailer->id]);

    Livewire::test(DriversForm::class, ['driver' => $driver])
        ->assertSet('driverData.tractor_id', $tractor->id)
        ->assertSet('driverData.trailer_id', $trailer->id)
        ->set('driverData.tractor_id', $newTractor->id)
        ->set('driverData.trailer_id', '')
        ->call('save')
        ->assertRedirect(route('drivers.index'));

    expect($driver->fresh()->vehicles->pluck('id')->all())->toBe([$newTractor->id]);
});

test('removing a saved document deletes the media', function () {
    Storage::fake('driver_documents');

    $driver = Driver::factory()->create()->fresh();
    $file = UploadedFile::fake()->image('license-front.jpg');
    $driver->addMedia($file->getPathname())
        ->usingName('license-front.jpg')
        ->preservingOriginal()
        ->toMediaCollection(Driver::MEDIA_DRIVING_LICENSE_FRONT);

    expect($driver->getFirstMedia(Driver::MEDIA_DRIVING_LICENSE_FRONT))->not->toBeNull();

    Livewire::test(DriversForm::class, ['driver' => $driver])
        ->call('removeDocument', 'driving_license_document_front');

    expect($driver->refresh()->getFirstMedia(Driver::MEDIA_DRIVING_LICENSE_FRONT))->toBeNull();
});
