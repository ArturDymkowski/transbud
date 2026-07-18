<?php

use App\Models\Driver;
use App\Models\User;
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

test('authenticated user can download an existing driver document', function () {
    Storage::fake('driver_documents');

    $driver = Driver::factory()->create();
    $file = UploadedFile::fake()->image('license-front.jpg');
    $driver->addMedia($file->getPathname())
        ->usingName('license-front.jpg')
        ->preservingOriginal()
        ->toMediaCollection(Driver::MEDIA_DRIVING_LICENSE_FRONT);

    $media = $driver->getFirstMedia(Driver::MEDIA_DRIVING_LICENSE_FRONT);

    $this->actingAs(User::factory()->create())
        ->get(route('driver-documents.show', $media))
        ->assertOk();
});

test('media belonging to a different model type cannot be downloaded', function () {
    $media = makeMediaFor(User::factory()->create());

    $this->actingAs(User::factory()->create())
        ->get(route('driver-documents.show', $media))
        ->assertNotFound();
});
