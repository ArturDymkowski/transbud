<?php

namespace App\Livewire\Calendars;

use App\Enums\DeliveryTransportSetStatusEnum;
use App\Enums\VehicleTypeEnum;
use App\Livewire\Concerns\WithDeliveryGoodsSync;
use App\Livewire\Concerns\WithDeliveryLookupOptions;
use App\Livewire\Concerns\WithDeliveryStatusComputation;
use App\Livewire\Concerns\WithDriverVehicleOptions;
use App\Models\Delivery;
use App\Models\DeliveryTransportSet;
use App\Models\Driver;
use App\Models\Good;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Livewire\Component;

class DeliveriesCalendar extends Component
{
    use WithDeliveryGoodsSync, WithDeliveryLookupOptions, WithDeliveryStatusComputation, WithDriverVehicleOptions;

    public bool $isOpen = false;

    public ?int $deliveryId = null;

    public ?int $transportSetId = null;

    public array $deliveryData = [];

    public array $transportSetData = [];

    public function getEvents(string $start, string $end): array
    {
        return DeliveryTransportSet::query()
            ->whereNotNull('loading_at')
            ->whereBetween('loading_at', [$start, $end])
            ->with(['delivery', 'driver', 'vehicle', 'trailer'])
            ->get()
            ->map(fn (DeliveryTransportSet $transportSet) => [
                'id' => $transportSet->id,
                'title' => $this->eventTitle($transportSet),
                'start' => $transportSet->loading_at?->toIso8601String(),
                'end' => $transportSet->loading_at?->toIso8601String(),
                'color' => $transportSet->delivery->status->color(),
                'allDay' => true,
            ])
            ->all();
    }

    private function eventTitle(DeliveryTransportSet $transportSet): string
    {
        $parts = array_filter([
            $transportSet->delivery->number,
            $transportSet->driver?->name,
        ]);

        return implode(' · ', $parts);
    }

    public function openTransportSet(int $transportSetId): void
    {
        $this->authorize('deliveries.edit');

        $transportSet = DeliveryTransportSet::with(['delivery', 'goods'])->findOrFail($transportSetId);

        $this->deliveryId = $transportSet->delivery_id;
        $this->transportSetId = $transportSet->id;

        $this->deliveryData = $transportSet->delivery->only([
            'number', 'contractor_id', 'contractor_address_id', 'loading_address',
        ]);

        $this->transportSetData = [
            'driver_id' => $transportSet->driver_id,
            'vehicle_id' => $transportSet->vehicle_id,
            'trailer_id' => $transportSet->trailer_id,
            'loading_at' => $transportSet->loading_at?->format('Y-m-d H:i'),
            'unloading_at' => $transportSet->unloading_at?->format('Y-m-d H:i'),
            'status' => $transportSet->status->value,
            'goods' => $transportSet->goods->map(fn ($good) => [
                'id' => $good->id,
                'good_id' => $good->good_id,
                'unit_id' => $good->unit_id,
                'quantity' => (string) $good->quantity,
            ])->all(),
        ];

        $this->resetValidation();
        $this->isOpen = true;
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->reset('deliveryId', 'transportSetId', 'deliveryData', 'transportSetData');
    }

    protected function rules(): array
    {
        $draft = DeliveryTransportSetStatusEnum::DRAFT->value;

        return [
            'deliveryData.number' => 'required|string|max:255|unique:deliveries,number,'.($this->deliveryId ?? 'NULL'),
            'deliveryData.contractor_id' => 'required|exists:contractors,id',
            'deliveryData.contractor_address_id' => [
                'required',
                Rule::exists('contractor_addresses', 'id')->where('contractor_id', $this->deliveryData['contractor_id'] ?? null),
            ],
            'deliveryData.loading_address' => 'required|string|max:255',

            'transportSetData.driver_id' => [
                'nullable',
                'required_unless:transportSetData.status,'.$draft,
                'exists:drivers,id',
            ],
            'transportSetData.vehicle_id' => [
                'nullable',
                'required_unless:transportSetData.status,'.$draft,
                Rule::exists('vehicles', 'id')->where('type', VehicleTypeEnum::TRACTOR->value),
            ],
            'transportSetData.trailer_id' => [
                'nullable',
                'required_unless:transportSetData.status,'.$draft,
                Rule::exists('vehicles', 'id')->where('type', VehicleTypeEnum::TRAILER->value),
            ],
            'transportSetData.loading_at' => [
                'nullable',
                'required_unless:transportSetData.status,'.$draft,
                'date',
            ],
            'transportSetData.unloading_at' => [
                'nullable',
                'required_unless:transportSetData.status,'.$draft,
                'date',
                'after_or_equal:transportSetData.loading_at',
            ],
            'transportSetData.status' => ['required', new Enum(DeliveryTransportSetStatusEnum::class)],

            'transportSetData.goods' => 'array',
            'transportSetData.goods.*.good_id' => 'required|exists:goods,id',
            'transportSetData.goods.*.unit_id' => 'required|exists:units,id',
            'transportSetData.goods.*.quantity' => 'required|numeric|min:0.01',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'deliveryData.number' => __('deliveries.number'),
            'deliveryData.contractor_id' => __('deliveries.contractor'),
            'deliveryData.contractor_address_id' => __('deliveries.contractor_address'),
            'deliveryData.loading_address' => __('deliveries.loading_address'),

            'transportSetData.driver_id' => __('deliveries.transport_set.driver'),
            'transportSetData.vehicle_id' => __('deliveries.transport_set.vehicle'),
            'transportSetData.trailer_id' => __('deliveries.transport_set.trailer'),
            'transportSetData.loading_at' => __('deliveries.transport_set.loading_at'),
            'transportSetData.unloading_at' => __('deliveries.transport_set.unloading_at'),
            'transportSetData.status' => __('deliveries.transport_set_status.status'),

            'transportSetData.goods.*.good_id' => __('deliveries.goods.good'),
            'transportSetData.goods.*.unit_id' => __('deliveries.goods.unit'),
            'transportSetData.goods.*.quantity' => __('deliveries.goods.quantity'),
        ];
    }

    public function updatedDeliveryData(mixed $value, string $key): void
    {
        if ($key === 'contractor_id') {
            $this->deliveryData['contractor_address_id'] = null;
        }
    }

    public function updatedTransportSetData(mixed $value, string $key): void
    {
        if ($key === 'driver_id') {
            $driver = Driver::find($value);
            $this->transportSetData['vehicle_id'] = $driver?->tractor()?->id;
            $this->transportSetData['trailer_id'] = $driver?->trailer()?->id;

            return;
        }

        if (str_starts_with($key, 'goods.') && str_ends_with($key, '.good_id')) {
            $goodIndex = (int) explode('.', $key)[1];
            $good = Good::find($value);
            $this->transportSetData['goods'][$goodIndex]['unit_id'] = $good?->default_unit_id;
        }
    }

    public function addGoodRow(): void
    {
        $this->transportSetData['goods'][] = ['good_id' => null, 'unit_id' => null, 'quantity' => ''];
    }

    public function removeGoodRow(int $goodIndex): void
    {
        unset($this->transportSetData['goods'][$goodIndex]);
        $this->transportSetData['goods'] = array_values($this->transportSetData['goods']);
    }

    public function save(): void
    {
        $this->authorize('deliveries.edit');

        $this->validate();

        DB::transaction(function () {
            $delivery = Delivery::findOrFail($this->deliveryId);
            $delivery->fill($this->deliveryData);

            $transportSet = DeliveryTransportSet::findOrFail($this->transportSetId);
            $transportSet->fill([
                'driver_id' => $this->transportSetData['driver_id'] ?: null,
                'vehicle_id' => $this->transportSetData['vehicle_id'] ?: null,
                'trailer_id' => $this->transportSetData['trailer_id'] ?: null,
                'loading_at' => $this->transportSetData['loading_at'] ?: null,
                'unloading_at' => $this->transportSetData['unloading_at'] ?: null,
                'status' => $this->transportSetData['status'],
            ]);
            $transportSet->save();

            $this->syncGoods($transportSet, $this->transportSetData['goods'] ?? []);

            $delivery->status = $this->computeDeliveryStatus($delivery->transportSets()->pluck('status'));
            $delivery->save();
        });

        $this->closeModal();

        $this->dispatch('notify', message: __('labels.general.saved_success'));
        $this->dispatch('calendar-refresh');
    }

    public function render()
    {
        return view('livewire.calendars.deliveries-calendar');
    }
}
