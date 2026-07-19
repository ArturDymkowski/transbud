<?php

namespace App\Livewire\Forms;

use App\Enums\CountriesEnum;
use App\Livewire\Concerns\WithSavedRedirect;
use App\Models\Driver;
use Illuminate\Http\Response;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;

use Illuminate\Validation\Rules\Enum;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DriversForm extends Component
{
    use WithFileUploads, WithSavedRedirect;

    public array $driverData = [];
    public ?\App\Models\Driver $driver = null;

    public function mount(\App\Models\Driver $driver = null)
    {
        if ($driver && $driver->exists) {
            $this->driver = $driver;
        } else {
            $this->driver = new \App\Models\Driver();
        }

        $this->driverData = $this->driver->only([
            'name', 'phone', 'pesel', 'country', 'zipcode',
            'city', 'street', 'house_nr', 'apartment_nr', 'extra_info',
            'driving_license_number', 'driving_license_expiry_date',
            'identity_card_number', 'identity_card_expiry_date',
        ]);
    }

    protected function rules(): array
    {
        return [
            'driverData.name' => 'required|string|max:255',
            'driverData.phone' => 'required|string|max:30',
            'driverData.pesel' => 'required|string|size:11|unique:drivers,pesel,' . ($this->driver?->id ?? 'NULL'),
            'driverData.country' => ['nullable', new Enum(CountriesEnum::class)],
            'driverData.zipcode' => 'nullable|string|max:20',
            'driverData.city' => 'nullable|string|max:100',
            'driverData.street' => 'nullable|string|max:100',
            'driverData.house_nr' => 'nullable|string|max:20',
            'driverData.apartment_nr' => 'nullable|string|max:20',
            'driverData.extra_info' => 'nullable|string',
            'driverData.driving_license_number' => 'required|string|unique:drivers,driving_license_number,' . ($this->driver?->id ?? 'NULL'),
            'driverData.driving_license_expiry_date' => 'required|date',
            'driverData.identity_card_expiry_date' => 'required|date',

            'driverData.driving_license_document_front' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'driverData.driving_license_document_back'  => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'driverData.identity_card_document_front'   => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'driverData.identity_card_document_back'    => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'driverData.name' => __('drivers.name'),
            'driverData.phone' => __('drivers.phone'),
            'driverData.pesel' => __('drivers.pesel'),
            'driverData.country' => __('labels.address.country'),
            'driverData.zipcode' => __('labels.address.zipcode'),
            'driverData.city' => __('labels.address.city'),
            'driverData.street' => __('labels.address.street'),
            'driverData.house_nr' => __('labels.address.house_nr'),
            'driverData.apartment_nr' => __('labels.address.apartment_nr'),
            'driverData.extra_info' => __('drivers.extra_info'),

            'driverData.driving_license_number' => __('drivers.driving_license_number'),
            'driverData.driving_license_expiry_date' => __('drivers.driving_license_expiry_date'),
            'driverData.identity_card_expiry_date' => __('drivers.identity_card_expiry_date'),
            'driverData.driving_license_document_front' => __('drivers.document_front'),
            'driverData.driving_license_document_back' => __('drivers.document_back'),
            'driverData.identity_card_document_front' => __('drivers.document_front'),
            'driverData.identity_card_document_back' => __('drivers.document_back'),
        ];
    }

    public function save()
    {
        $this->validate();

        $fileKeys = array_keys($this->mediaCollectionsMap());
        $files = array_intersect_key($this->driverData, array_flip($fileKeys));
        $attributes = array_diff_key($this->driverData, array_flip($fileKeys));

        $isUpdate = $this->driver->exists;

        if ($isUpdate) {
            $this->driver->update($attributes);
        } else {
            $this->driver->fill($attributes);
            $this->driver->save();
        }

        foreach ($this->mediaCollectionsMap() as $key => $collection) {
            $this->attachMedia($files[$key] ?? null, $collection);
        }

        return $this->flashSavedAndRedirect($isUpdate, 'drivers.index');
    }

    public function updated(string $property): void
    {
        $fileKeys = array_keys($this->mediaCollectionsMap());

        foreach ($fileKeys as $key) {
            if ($property === "driverData.{$key}") {
                $this->validateOnly($property);
            }
        }
    }

    private function attachMedia(mixed $file, string $collection): void
    {
        if (! $file instanceof TemporaryUploadedFile) {
            return;
        }

        $this->driver
            ->addMedia($file->getRealPath())
            ->usingName($file->getClientOriginalName())
            ->usingFileName($file->hashName())
            ->toMediaCollection($collection);
    }

    private function mediaCollectionsMap(): array
    {
        return [
            'driving_license_document_front' => Driver::MEDIA_DRIVING_LICENSE_FRONT,
            'driving_license_document_back'  => Driver::MEDIA_DRIVING_LICENSE_BACK,
            'identity_card_document_front'   => Driver::MEDIA_IDENTITY_CARD_FRONT,
            'identity_card_document_back'    => Driver::MEDIA_IDENTITY_CARD_BACK,
        ];
    }

    #[Computed]
    public function existingMedia(): array
    {
        if (! $this->driver?->exists) {
            return [];
        }

        $result = [];
        foreach ($this->mediaCollectionsMap() as $key => $collection) {
            $media = $this->driver->getFirstMedia($collection);

            $result[$key] = $media ? [
                'id' => $media->id,
                'mime_type' => $media->mime_type,
            ] : null;
        }

        return $result;
    }

    public function removeDocument(string $key): void
    {
        // nie zapisany plik
        if (isset($this->driverData[$key]) && $this->driverData[$key] instanceof TemporaryUploadedFile) {
            unset($this->driverData[$key]);
            $this->resetValidation("driverData.{$key}");
            return;
        }

        // zapisany plik
        $collectionsMap = $this->mediaCollectionsMap();

        if (! isset($collectionsMap[$key]) || ! $this->driver?->exists) {
            return;
        }

        $media = $this->driver->getFirstMedia($collectionsMap[$key]);
        $media?->delete();

        $this->driver->unsetRelation('media');
        unset($this->existingMedia);
    }

    public function downloadDocument(string $key): BinaryFileResponse|null
    {
        $collectionsMap = $this->mediaCollectionsMap();

        if (! isset($collectionsMap[$key]) || ! $this->driver?->exists) {
            return null;
        }

        $media = $this->driver->getFirstMedia($collectionsMap[$key]);

        if (! $media) {
            return null;
        }

        return response()->download($media->getPath(), $media->file_name);
    }

    public function render()
    {
        return view('livewire.forms.drivers-form');
    }
}
