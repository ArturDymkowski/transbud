<?php

namespace App\Livewire\Forms;

use App\Enums\DeliveryStatusEnum;
use App\Enums\DeliveryTransportSetStatusEnum;
use App\Enums\VehicleTypeEnum;
use App\Livewire\Concerns\WithDriverVehicleOptions;
use App\Livewire\Concerns\WithSavedRedirect;
use App\Models\Contractor;
use App\Models\ContractorAddress;
use App\Models\Delivery;
use App\Models\Driver;
use App\Models\Good;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class DeliveriesForm extends Component
{
    use WithDriverVehicleOptions, WithFileUploads, WithSavedRedirect;

    public array $deliveryData = [];

    public array $goodsData = [];

    public array $transportSetsData = [];

    public array $newDocuments = [];

    public ?Delivery $delivery = null;

    public function mount(?Delivery $delivery = null)
    {
        $this->delivery = ($delivery && $delivery->exists) ? $delivery : new Delivery;

        $this->deliveryData = [
            'number' => '',
            'contractor_id' => null,
            'contractor_address_id' => null,
            'loading_address' => '',
            'status' => array_key_first(DeliveryStatusEnum::getOptions()),
        ];

        $this->addGoodRow();
        $this->addTransportSetRow();
    }

    protected function rules(): array
    {
        return [
            'deliveryData.number' => 'required|string|max:255|unique:deliveries,number,'.($this->delivery?->id ?? 'NULL'),
            'deliveryData.contractor_id' => 'required|exists:contractors,id',
            'deliveryData.contractor_address_id' => [
                'required',
                Rule::exists('contractor_addresses', 'id')->where('contractor_id', $this->deliveryData['contractor_id'] ?? null),
            ],
            'deliveryData.loading_address' => 'required|string|max:255',
            'deliveryData.status' => ['required', new Enum(DeliveryStatusEnum::class)],

            'goodsData' => 'required|array|min:1',
            'goodsData.*.good_id' => 'required|exists:goods,id',
            'goodsData.*.unit_id' => 'required|exists:units,id',
            'goodsData.*.quantity' => 'required|numeric|min:0.01',

            'transportSetsData' => 'required|array|min:1',
            'transportSetsData.*.driver_id' => 'required|exists:drivers,id',
            'transportSetsData.*.vehicle_id' => ['required', Rule::exists('vehicles', 'id')->where('type', VehicleTypeEnum::TRACTOR->value)],
            'transportSetsData.*.trailer_id' => ['required', Rule::exists('vehicles', 'id')->where('type', VehicleTypeEnum::TRAILER->value)],
            'transportSetsData.*.loading_at' => 'required|date',
            'transportSetsData.*.unloading_at' => 'required|date|after_or_equal:transportSetsData.*.loading_at',
            'transportSetsData.*.status' => ['required', new Enum(DeliveryTransportSetStatusEnum::class)],

            'newDocuments.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'deliveryData.number' => __('deliveries.number'),
            'deliveryData.contractor_id' => __('deliveries.contractor'),
            'deliveryData.contractor_address_id' => __('deliveries.contractor_address'),
            'deliveryData.loading_address' => __('deliveries.loading_address'),
            'deliveryData.status' => __('deliveries.status.status'),

            'goodsData.*.good_id' => __('deliveries.goods.good'),
            'goodsData.*.unit_id' => __('deliveries.goods.unit'),
            'goodsData.*.quantity' => __('deliveries.goods.quantity'),

            'transportSetsData.*.driver_id' => __('deliveries.transport_set.driver'),
            'transportSetsData.*.vehicle_id' => __('deliveries.transport_set.vehicle'),
            'transportSetsData.*.trailer_id' => __('deliveries.transport_set.trailer'),
            'transportSetsData.*.loading_at' => __('deliveries.transport_set.loading_at'),
            'transportSetsData.*.unloading_at' => __('deliveries.transport_set.unloading_at'),
            'transportSetsData.*.status' => __('deliveries.transport_set_status.status'),

            'newDocuments.*' => __('deliveries.documents'),
        ];
    }

    public function updated(string $property): void
    {
        if ($property === 'deliveryData.contractor_id') {
            $this->deliveryData['contractor_address_id'] = null;
        }

        if (preg_match('/^goodsData\.(\d+)\.good_id$/', $property, $matches)) {
            $index = (int) $matches[1];
            $options = $this->goodUnitOptions($this->goodsData[$index]['good_id'] ?? null);
            $ids = array_keys($options);
            $this->goodsData[$index]['unit_id'] = count($ids) === 1 ? $ids[0] : null;
        }
    }

    public function addGoodRow(): void
    {
        $this->goodsData[] = ['good_id' => null, 'unit_id' => null, 'quantity' => ''];
    }

    public function removeGoodRow(int $index): void
    {
        unset($this->goodsData[$index]);
        $this->goodsData = array_values($this->goodsData);
    }

    public function addTransportSetRow(): void
    {
        $this->transportSetsData[] = [
            'driver_id' => null,
            'vehicle_id' => null,
            'trailer_id' => null,
            'loading_at' => '',
            'unloading_at' => '',
            'status' => DeliveryTransportSetStatusEnum::ASSIGNED->value,
        ];
    }

    public function removeTransportSetRow(int $index): void
    {
        unset($this->transportSetsData[$index]);
        $this->transportSetsData = array_values($this->transportSetsData);
    }

    public function removeNewDocument(int $index): void
    {
        unset($this->newDocuments[$index]);
        $this->newDocuments = array_values($this->newDocuments);
    }

    public function goodUnitOptions(?int $goodId): array
    {
        if (! $goodId) {
            return [];
        }

        $good = Good::with(['units', 'defaultUnit'])->find($goodId);

        if (! $good) {
            return [];
        }

        return $good->units
            ->push($good->defaultUnit)
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function contractorAddressOptions(?int $contractorId): array
    {
        $options = ['' => __('labels.general.not_selected')];

        if (! $contractorId) {
            return $options;
        }

        foreach (ContractorAddress::where('contractor_id', $contractorId)->get() as $address) {
            $options[$address->id] = $address->fullAddress;
        }

        return $options;
    }

    #[Computed]
    public function contractorOptions(): array
    {
        $options = ['' => __('labels.general.not_selected')];

        foreach (Contractor::orderBy('name')->get() as $contractor) {
            $options[$contractor->id] = $contractor->name;
        }

        return $options;
    }

    #[Computed]
    public function driverOptions(): array
    {
        $options = ['' => __('labels.general.not_selected')];

        foreach (Driver::orderBy('name')->get() as $driver) {
            $options[$driver->id] = $driver->name;
        }

        return $options;
    }

    #[Computed]
    public function goodOptions(): array
    {
        $options = ['' => __('labels.general.not_selected')];

        foreach (Good::where('is_active', true)->orderBy('name')->get() as $good) {
            $options[$good->id] = $good->name;
        }

        return $options;
    }

    public function save()
    {
        $this->authorize('deliveries.create');

        $this->validate();

        DB::transaction(function () {
            $this->delivery->fill($this->deliveryData);
            $this->delivery->save();

            foreach ($this->goodsData as $good) {
                $this->delivery->goods()->create($good);
            }

            foreach ($this->transportSetsData as $transportSet) {
                $this->delivery->transportSets()->create($transportSet);
            }

            foreach ($this->newDocuments as $file) {
                if (! $file instanceof TemporaryUploadedFile) {
                    continue;
                }

                $this->delivery
                    ->addMedia($file->getRealPath())
                    ->usingName($file->getClientOriginalName())
                    ->usingFileName($file->hashName())
                    ->toMediaCollection(Delivery::MEDIA_DOCUMENTS);
            }
        });

        return $this->flashSavedAndRedirect(false, 'deliveries.index');
    }

    public function render()
    {
        return view('livewire.forms.deliveries-form');
    }
}
