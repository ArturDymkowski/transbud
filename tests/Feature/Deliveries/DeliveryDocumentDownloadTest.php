<?php

use App\Models\Delivery;
use App\Models\Driver;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

function makeDeliveryMediaFor(Model $model): Media
{
    return Media::create([
        'model_type' => $model::class,
        'model_id' => $model->id,
        'collection_name' => 'default',
        'name' => 'file',
        'file_name' => 'file.pdf',
        'mime_type' => 'application/pdf',
        'disk' => 'delivery_documents',
        'size' => 1,
        'manipulations' => [],
        'custom_properties' => [],
        'generated_conversions' => [],
        'responsive_images' => [],
    ]);
}

test('guest is redirected from a delivery document download link', function () {
    $media = makeDeliveryMediaFor(Delivery::factory()->create());

    $this->get(route('delivery-documents.show', $media))
        ->assertRedirect(route('login'));
});

test('user with deliveries.view permission can download an existing delivery document', function () {
    Storage::fake('delivery_documents');
    $this->seed([PermissionSeeder::class, RoleSeeder::class]);

    $delivery = Delivery::factory()->create();
    $file = UploadedFile::fake()->image('document.jpg');
    $delivery->addMedia($file->getPathname())
        ->usingName('document.jpg')
        ->preservingOriginal()
        ->toMediaCollection(Delivery::MEDIA_DOCUMENTS);

    $media = $delivery->getFirstMedia(Delivery::MEDIA_DOCUMENTS);

    $user = User::factory()->create();
    $user->assignRole('User');

    $this->actingAs($user)
        ->get(route('delivery-documents.show', $media))
        ->assertOk();
});

test('authenticated user without deliveries.view permission cannot download a delivery document', function () {
    Storage::fake('delivery_documents');
    $this->seed([PermissionSeeder::class, RoleSeeder::class]);

    $delivery = Delivery::factory()->create();
    $file = UploadedFile::fake()->image('document.jpg');
    $delivery->addMedia($file->getPathname())
        ->usingName('document.jpg')
        ->preservingOriginal()
        ->toMediaCollection(Delivery::MEDIA_DOCUMENTS);

    $media = $delivery->getFirstMedia(Delivery::MEDIA_DOCUMENTS);

    // no role assigned => no permissions at all
    $this->actingAs(User::factory()->create())
        ->get(route('delivery-documents.show', $media))
        ->assertForbidden();
});

test('media belonging to a different model type cannot be downloaded as a delivery document', function () {
    $this->seed([PermissionSeeder::class, RoleSeeder::class]);

    $media = makeDeliveryMediaFor(Driver::factory()->create());

    $user = User::factory()->create();
    $user->assignRole('User');

    $this->actingAs($user)
        ->get(route('delivery-documents.show', $media))
        ->assertNotFound();
});
