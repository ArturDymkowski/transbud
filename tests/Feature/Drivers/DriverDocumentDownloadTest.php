<?php

use App\Models\Driver;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

function makeMediaFor(Model $model): Media
{
    return Media::create([
        'model_type' => $model::class,
        'model_id' => $model->id,
        'collection_name' => 'default',
        'name' => 'file',
        'file_name' => 'file.jpg',
        'mime_type' => 'image/jpeg',
        'disk' => 'driver_documents',
        'size' => 1,
        'manipulations' => [],
        'custom_properties' => [],
        'generated_conversions' => [],
        'responsive_images' => [],
    ]);
}

test('guest is redirected from a document download link', function () {
    $media = makeMediaFor(Driver::factory()->create());

    $this->get(route('driver-documents.show', $media))
        ->assertRedirect(route('login'));
});

test('user with drivers.view permission can download an existing driver document', function () {
    Storage::fake('driver_documents');
    $this->seed([PermissionSeeder::class, RoleSeeder::class]);

    $driver = Driver::factory()->create();
    $file = UploadedFile::fake()->image('license-front.jpg');
    $driver->addMedia($file->getPathname())
        ->usingName('license-front.jpg')
        ->preservingOriginal()
        ->toMediaCollection(Driver::MEDIA_DRIVING_LICENSE_FRONT);

    $media = $driver->getFirstMedia(Driver::MEDIA_DRIVING_LICENSE_FRONT);

    $user = User::factory()->create();
    $user->assignRole('User');

    $this->actingAs($user)
        ->get(route('driver-documents.show', $media))
        ->assertOk();
});

test('authenticated user without drivers.view permission cannot download a driver document', function () {
    Storage::fake('driver_documents');
    $this->seed([PermissionSeeder::class, RoleSeeder::class]);

    $driver = Driver::factory()->create();
    $file = UploadedFile::fake()->image('license-front.jpg');
    $driver->addMedia($file->getPathname())
        ->usingName('license-front.jpg')
        ->preservingOriginal()
        ->toMediaCollection(Driver::MEDIA_DRIVING_LICENSE_FRONT);

    $media = $driver->getFirstMedia(Driver::MEDIA_DRIVING_LICENSE_FRONT);

    // no role assigned => no permissions at all
    $this->actingAs(User::factory()->create())
        ->get(route('driver-documents.show', $media))
        ->assertForbidden();
});

test('media belonging to a different model type cannot be downloaded', function () {
    $this->seed([PermissionSeeder::class, RoleSeeder::class]);

    $media = makeMediaFor(User::factory()->create());

    $user = User::factory()->create();
    $user->assignRole('User');

    $this->actingAs($user)
        ->get(route('driver-documents.show', $media))
        ->assertNotFound();
});
