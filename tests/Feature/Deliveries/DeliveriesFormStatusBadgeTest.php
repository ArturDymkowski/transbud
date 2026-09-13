<?php

use App\Enums\DeliveryStatusEnum;
use App\Livewire\Forms\DeliveriesForm;
use App\Models\Delivery;
use Livewire\Livewire;

beforeEach(function () {
    actingAsAdmin();
});

test('the delivery edit form shows the status badge, like the show page does', function () {
    $delivery = Delivery::factory()->create(['status' => DeliveryStatusEnum::IN_PROGRESS]);

    Livewire::test(DeliveriesForm::class, ['delivery' => $delivery])
        ->assertSee(DeliveryStatusEnum::IN_PROGRESS->label());
});

test('the delivery create form has no status badge yet', function () {
    Livewire::test(DeliveriesForm::class)
        ->assertDontSee(__('deliveries.status.status'));
});
