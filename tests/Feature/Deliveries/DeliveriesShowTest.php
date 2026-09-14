<?php

use App\Enums\DeliveryTransportSetStatusEnum;
use App\Models\Contractor;
use App\Models\ContractorAddress;
use App\Models\Delivery;
use App\Models\DeliveryTransportSet;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = actingAsAdmin();
});

test('the transport set status is rendered as a colored badge, not plain text', function () {
    $contractor = Contractor::factory()->create();
    $address = ContractorAddress::factory()->create(['contractor_id' => $contractor->id]);
    $delivery = Delivery::factory()->create([
        'contractor_id' => $contractor->id,
        'contractor_address_id' => $address->id,
    ]);
    $transportSet = DeliveryTransportSet::factory()->create([
        'delivery_id' => $delivery->id,
        'status' => DeliveryTransportSetStatusEnum::ASSIGNED,
    ]);

    $this->get(route('deliveries.show', $delivery))
        ->assertOk()
        ->assertSee($transportSet->status->color(), false);
});

test('an attached document is shown as a download link only, without delete or upload controls', function () {
    Storage::fake('delivery_documents');

    $delivery = Delivery::factory()->create();
    $delivery->addMedia(UploadedFile::fake()->image('invoice.jpg'))
        ->usingName('invoice.jpg')
        ->preservingOriginal()
        ->toMediaCollection(Delivery::MEDIA_DOCUMENTS);
    $media = $delivery->getFirstMedia(Delivery::MEDIA_DOCUMENTS);

    $this->get(route('deliveries.show', $delivery))
        ->assertOk()
        ->assertSee('invoice.jpg')
        ->assertSee(route('delivery-documents.show', $media), false)
        ->assertDontSee('deleteDocument', false)
        ->assertDontSee('wire:model="newDocuments"', false);
});
