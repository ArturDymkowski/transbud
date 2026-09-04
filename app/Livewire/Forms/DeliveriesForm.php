<?php

namespace App\Livewire\Forms;

use App\Enums\CountriesEnum;
use App\Enums\CurrencyEnum;
use App\Enums\DeliveryTransportSetStatusEnum;
use App\Enums\VehicleTypeEnum;
use App\Livewire\Concerns\WithDeliveryGoodsSync;
use App\Livewire\Concerns\WithDeliveryLookupOptions;
use App\Livewire\Concerns\WithDeliveryResourceAvailability;
use App\Livewire\Concerns\WithDeliveryStatusComputation;
use App\Livewire\Concerns\WithDemoLimits;
use App\Livewire\Concerns\WithDriverVehicleOptions;
use App\Livewire\Concerns\WithSavedRedirect;
use App\Models\ContractorAddress;
use App\Models\Delivery;
use App\Models\DeliveryGood;
use App\Models\DeliveryTransportSet;
use App\Models\Driver;
use App\Models\Good;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class DeliveriesForm extends Component
{
    use WithDeliveryGoodsSync, WithDeliveryLookupOptions, WithDeliveryResourceAvailability, WithDeliveryStatusComputation, WithDemoLimits, WithDriverVehicleOptions, WithFileUploads, WithSavedRedirect;

    private const NUMBER_PREFIX = 'DOS-';

    public array $deliveryData = [];

    public array $transportSetsData = [];

    public array $newDocuments = [];

    public bool $showCreateAddressModal = false;

    public array $createAddressData = [];

    public ?Delivery $delivery = null;

    public function mount(?Delivery $delivery = null)
    {
        $this->delivery = ($delivery && $delivery->exists) ? $delivery : new Delivery;

        if ($this->delivery->exists) {
            $this->deliveryData = $this->delivery->only([
                'number', 'contractor_id', 'contractor_address_id', 'loading_address',
            ]);
            $this->deliveryData['freight_amount'] = $this->delivery->freight_amount !== null
                ? number_format($this->delivery->freight_amount / 100, 2, '.', '')
                : '';
            $this->deliveryData['currency'] = $this->delivery->currency->value;

            $this->transportSetsData = $this->delivery->transportSets->map(fn ($transportSet) => [
                'id' => $transportSet->id,
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
            ])->all();
        } else {
            $this->deliveryData = [
                'number' => $this->generateDeliveryNumber(),
                'contractor_id' => null,
                'contractor_address_id' => null,
                'loading_address' => '',
                'freight_amount' => '',
                'currency' => CurrencyEnum::PLN->value,
            ];
        }
    }

    protected function rules(): array
    {
        $draft = DeliveryTransportSetStatusEnum::DRAFT->value;

        return [
            'deliveryData.number' => 'required|string|max:255|unique:deliveries,number,'.($this->delivery?->id ?? 'NULL'),
            'deliveryData.contractor_id' => 'required|exists:contractors,id',
            'deliveryData.contractor_address_id' => [
                'required',
                Rule::exists('contractor_addresses', 'id')->where('contractor_id', $this->deliveryData['contractor_id'] ?? null),
            ],
            'deliveryData.loading_address' => 'required|string|max:255',
            'deliveryData.freight_amount' => 'nullable|numeric|min:0',
            'deliveryData.currency' => ['required', new Enum(CurrencyEnum::class)],

            'transportSetsData' => 'array',
            'transportSetsData.*.driver_id' => [
                'nullable',
                'required_unless:transportSetsData.*.status,'.$draft,
                'exists:drivers,id',
                function (string $attribute, mixed $value, Closure $fail) {
                    $row = $this->transportSetsData[$this->rowIndexFromAttribute($attribute)];
                    $this->validateResourceAvailability($fail, 'driver_id', $value, $row['loading_at'] ?? null, $row['unloading_at'] ?? null, $this->delivery?->id, __('deliveries.transport_set.driver_busy'));
                },
            ],
            'transportSetsData.*.vehicle_id' => [
                'nullable',
                'required_unless:transportSetsData.*.status,'.$draft,
                Rule::exists('vehicles', 'id')->where('type', VehicleTypeEnum::TRACTOR->value),
                function (string $attribute, mixed $value, Closure $fail) {
                    $row = $this->transportSetsData[$this->rowIndexFromAttribute($attribute)];
                    $this->validateResourceAvailability($fail, 'vehicle_id', $value, $row['loading_at'] ?? null, $row['unloading_at'] ?? null, $this->delivery?->id, __('deliveries.transport_set.vehicle_busy'));
                },
            ],
            'transportSetsData.*.trailer_id' => [
                'nullable',
                'required_unless:transportSetsData.*.status,'.$draft,
                Rule::exists('vehicles', 'id')->where('type', VehicleTypeEnum::TRAILER->value),
                function (string $attribute, mixed $value, Closure $fail) {
                    $row = $this->transportSetsData[$this->rowIndexFromAttribute($attribute)];
                    $this->validateResourceAvailability($fail, 'trailer_id', $value, $row['loading_at'] ?? null, $row['unloading_at'] ?? null, $this->delivery?->id, __('deliveries.transport_set.trailer_busy'));
                },
            ],
            'transportSetsData.*.loading_at' => [
                'nullable',
                'required_unless:transportSetsData.*.status,'.$draft,
                'date',
            ],
            'transportSetsData.*.unloading_at' => [
                'nullable',
                'required_unless:transportSetsData.*.status,'.$draft,
                'date',
                'after_or_equal:transportSetsData.*.loading_at',
            ],
            'transportSetsData.*.status' => ['required', new Enum(DeliveryTransportSetStatusEnum::class)],

            'transportSetsData.*.goods' => 'array',
            'transportSetsData.*.goods.*.good_id' => 'required|exists:goods,id',
            'transportSetsData.*.goods.*.unit_id' => 'required|exists:units,id',
            'transportSetsData.*.goods.*.quantity' => 'required|numeric|min:0.01',

            'newDocuments.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ];
    }

    protected function messages(): array
    {
        $required = trans('validation.required');

        return [
            'transportSetsData.*.driver_id.required_unless' => $required,
            'transportSetsData.*.vehicle_id.required_unless' => $required,
            'transportSetsData.*.trailer_id.required_unless' => $required,
            'transportSetsData.*.loading_at.required_unless' => $required,
            'transportSetsData.*.unloading_at.required_unless' => $required,
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'deliveryData.number' => __('deliveries.number'),
            'deliveryData.contractor_id' => __('deliveries.contractor'),
            'deliveryData.contractor_address_id' => __('deliveries.contractor_address'),
            'deliveryData.loading_address' => __('deliveries.loading_address'),
            'deliveryData.freight_amount' => __('deliveries.freight_amount'),
            'deliveryData.currency' => __('deliveries.currency'),

            'transportSetsData.*.driver_id' => __('deliveries.transport_set.driver'),
            'transportSetsData.*.vehicle_id' => __('deliveries.transport_set.vehicle'),
            'transportSetsData.*.trailer_id' => __('deliveries.transport_set.trailer'),
            'transportSetsData.*.loading_at' => __('deliveries.transport_set.loading_at'),
            'transportSetsData.*.unloading_at' => __('deliveries.transport_set.unloading_at'),
            'transportSetsData.*.status' => __('deliveries.transport_set_status.status'),

            'transportSetsData.*.goods.*.good_id' => __('deliveries.goods.good'),
            'transportSetsData.*.goods.*.unit_id' => __('deliveries.goods.unit'),
            'transportSetsData.*.goods.*.quantity' => __('deliveries.goods.quantity'),

            'newDocuments.*' => __('deliveries.documents'),
        ];
    }

    public function updatedDeliveryData(mixed $value, string $key): void
    {
        if ($key === 'contractor_id') {
            $this->deliveryData['contractor_address_id'] = null;
        }
    }

    public function updatedTransportSetsData(mixed $value, string $key): void
    {
        if (str_ends_with($key, '.driver_id')) {
            $index = (int) explode('.', $key)[0];
            $driver = Driver::find($value);
            $this->transportSetsData[$index]['vehicle_id'] = $driver?->tractor()?->id;
            $this->transportSetsData[$index]['trailer_id'] = $driver?->trailer()?->id;

            return;
        }

        if (str_ends_with($key, '.good_id') && str_contains($key, '.goods.')) {
            [$setIndex, , $goodIndex] = explode('.', $key);
            $good = Good::find($value);
            $this->transportSetsData[(int) $setIndex]['goods'][(int) $goodIndex]['unit_id'] = $good?->default_unit_id;
        }
    }

    public function driverOptionsFor(int $index): array
    {
        return collect($this->excludeUsedOptions($this->driverOptions, 'driver_id', $index))->except('')->all();
    }

    public function tractorOptionsFor(int $index): array
    {
        return collect($this->excludeUsedOptions($this->tractorOptions, 'vehicle_id', $index))->except('')->all();
    }

    public function trailerOptionsFor(int $index): array
    {
        return collect($this->excludeUsedOptions($this->trailerOptions, 'trailer_id', $index))->except('')->all();
    }

    private function excludeUsedOptions(array $options, string $field, int $currentIndex): array
    {
        $usedIds = collect($this->transportSetsData)
            ->reject(fn ($transportSet, $index) => $index === $currentIndex)
            ->pluck($field)
            ->filter()
            ->all();

        return collect($options)->except($usedIds)->all();
    }

    private function rowIndexFromAttribute(string $attribute): int
    {
        return (int) explode('.', $attribute)[1];
    }

    public function addTransportSetRow(): void
    {
        $this->transportSetsData[] = [
            'driver_id' => null,
            'vehicle_id' => null,
            'trailer_id' => null,
            'loading_at' => '',
            'unloading_at' => '',
            'status' => DeliveryTransportSetStatusEnum::DRAFT->value,
            'goods' => [],
        ];
    }

    public function removeTransportSetRow(int $index): void
    {
        unset($this->transportSetsData[$index]);
        $this->transportSetsData = array_values($this->transportSetsData);
    }

    public function addGoodRow(int $setIndex): void
    {
        $this->transportSetsData[$setIndex]['goods'][] = ['good_id' => null, 'unit_id' => null, 'quantity' => ''];
    }

    public function removeGoodRow(int $setIndex, int $goodIndex): void
    {
        unset($this->transportSetsData[$setIndex]['goods'][$goodIndex]);
        $this->transportSetsData[$setIndex]['goods'] = array_values($this->transportSetsData[$setIndex]['goods']);
    }

    public function removeNewDocument(int $index): void
    {
        unset($this->newDocuments[$index]);
        $this->newDocuments = array_values($this->newDocuments);
    }

    #[Computed]
    public function existingDocuments()
    {
        if (! $this->delivery->exists) {
            return collect();
        }

        return $this->delivery->getMedia(Delivery::MEDIA_DOCUMENTS);
    }

    public function deleteDocument(int $mediaId): void
    {
        $this->delivery->getMedia(Delivery::MEDIA_DOCUMENTS)
            ->firstWhere('id', $mediaId)
            ?->delete();

        $this->delivery->unsetRelation('media');
        unset($this->existingDocuments);

        $this->dispatch('notify', message: __('labels.general.deleted_success'));
    }

    public function openCreateAddressModal(): void
    {
        if (! ($this->deliveryData['contractor_id'] ?? null)) {
            return;
        }

        $this->createAddressData = [
            'country' => null,
            'zipcode' => '',
            'city' => '',
            'street' => '',
            'house_nr' => '',
            'apartment_nr' => '',
        ];
        $this->resetValidation();
        $this->showCreateAddressModal = true;
    }

    public function createAddress(): void
    {
        if (! ($this->deliveryData['contractor_id'] ?? null)) {
            return;
        }

        $this->authorize('contractor-addresses.create');

        $validated = $this->validate([
            'createAddressData.country' => ['required', new Enum(CountriesEnum::class)],
            'createAddressData.zipcode' => 'required|string|max:20',
            'createAddressData.city' => 'required|string|max:100',
            'createAddressData.street' => 'required|string|max:100',
            'createAddressData.house_nr' => 'nullable|string|max:20',
            'createAddressData.apartment_nr' => 'nullable|string|max:20',
        ], [], [
            'createAddressData.country' => __('labels.address.country'),
            'createAddressData.zipcode' => __('labels.address.zipcode'),
            'createAddressData.city' => __('labels.address.city'),
            'createAddressData.street' => __('labels.address.street'),
            'createAddressData.house_nr' => __('labels.address.house_nr'),
            'createAddressData.apartment_nr' => __('labels.address.apartment_nr'),
        ]);

        $address = ContractorAddress::create([
            ...$validated['createAddressData'],
            'contractor_id' => $this->deliveryData['contractor_id'],
        ]);

        $this->deliveryData['contractor_address_id'] = $address->id;
        $this->showCreateAddressModal = false;
        $this->reset('createAddressData');

        $this->dispatch('notify', message: __('labels.general.saved_success'));
    }

    private function generateDeliveryNumber(): string
    {
        $maxSequence = Delivery::query()
            ->where('number', 'like', self::NUMBER_PREFIX.'%')
            ->pluck('number')
            ->map(fn (string $number) => (int) substr($number, strlen(self::NUMBER_PREFIX)))
            ->max();

        return self::NUMBER_PREFIX.sprintf('%04d', ($maxSequence ?? 0) + 1);
    }

    public function save()
    {
        $isUpdate = $this->delivery->exists;

        $this->authorize($isUpdate ? 'deliveries.edit' : 'deliveries.create');

        if (! $isUpdate) {
            $this->ensureDemoRecordLimitsAllow(Delivery::class);
        }

        // Typing a comma as the decimal separator is allowed client-side (pl locale
        // convention), so normalize it before the `numeric` rule/float cast see it.
        if (isset($this->deliveryData['freight_amount'])) {
            $this->deliveryData['freight_amount'] = str_replace(',', '.', (string) $this->deliveryData['freight_amount']);
        }

        $this->validate();

        DB::transaction(function () use ($isUpdate) {
            if (! $isUpdate) {
                $this->deliveryData['number'] = $this->generateDeliveryNumber();
            }

            $this->deliveryData['freight_amount'] = filled($this->deliveryData['freight_amount'] ?? null)
                ? (int) round(((float) $this->deliveryData['freight_amount']) * 100)
                : null;

            $this->delivery->fill($this->deliveryData);
            $this->delivery->status = $this->computeDeliveryStatus(collect($this->transportSetsData)->pluck('status'));
            $this->delivery->save();

            $this->syncTransportSets();

            foreach ($this->newDocuments as $file) {
                if (! $file instanceof TemporaryUploadedFile) {
                    continue;
                }

                $this->ensureDemoDiskHasRoomFor('delivery_documents', $file);

                $this->delivery
                    ->addMedia($file->getRealPath())
                    ->usingName($file->getClientOriginalName())
                    ->usingFileName($file->hashName())
                    ->toMediaCollection(Delivery::MEDIA_DOCUMENTS);
            }
        });

        return $this->flashSavedAndRedirect($isUpdate, 'deliveries.index');
    }

    private function syncTransportSets(): void
    {
        $keepIds = [];

        foreach ($this->transportSetsData as $transportSetData) {
            $attributes = [
                'driver_id' => $transportSetData['driver_id'] ?: null,
                'vehicle_id' => $transportSetData['vehicle_id'] ?: null,
                'trailer_id' => $transportSetData['trailer_id'] ?: null,
                'loading_at' => $transportSetData['loading_at'] ?: null,
                'unloading_at' => $transportSetData['unloading_at'] ?: null,
                'status' => $transportSetData['status'],
            ];

            if (! empty($transportSetData['id'])) {
                $transportSet = $this->delivery->transportSets()->whereKey($transportSetData['id'])->first();

                if (! $transportSet) {
                    throw ValidationException::withMessages([
                        'transportSetsData' => trans('deliveries.transport_set.not_found'),
                    ]);
                }

                $previousStatus = $transportSet->status->value;
                $transportSet->update($attributes);

                if ($previousStatus !== (int) $attributes['status']) {
                    $this->recordStatusChange($transportSet, $attributes['status']);
                }
            } else {
                $transportSet = $this->delivery->transportSets()->create($attributes);
                $this->recordStatusChange($transportSet, $attributes['status']);
            }

            $keepIds[] = $transportSet->id;

            $this->syncGoods($transportSet, $transportSetData['goods'] ?? []);
        }

        $removedTransportSetIds = $this->delivery->transportSets()->whereNotIn('id', $keepIds)->pluck('id');

        DeliveryGood::whereIn('delivery_transport_set_id', $removedTransportSetIds)->delete();

        $this->delivery->transportSets()->whereNotIn('id', $keepIds)->delete();
    }

    private function recordStatusChange(DeliveryTransportSet $transportSet, int|string $status): void
    {
        $transportSet->statusHistories()->create([
            'status' => $status,
            'changed_by' => auth()->id(),
        ]);
    }

    public function render()
    {
        return view('livewire.forms.deliveries-form');
    }
}
