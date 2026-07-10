<?php

namespace App\Livewire\Forms;

use App\Enums\CountriesEnum;
use App\Models\Driver;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;

use Illuminate\Validation\Rules\Enum;

class DriversForm extends Component
{
    use WithFileUploads;

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
            'name', 'phone', 'pesel', 'country', 'region', 'zipcode',
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
            'driverData.region' => 'nullable|string|max:100',
            'driverData.zipcode' => 'nullable|string|max:20',
            'driverData.city' => 'nullable|string|max:100',
            'driverData.street' => 'nullable|string|max:100',
            'driverData.house_nr' => 'nullable|string|max:20',
            'driverData.apartment_nr' => 'nullable|string|max:20',
            'driverData.extra_info' => 'nullable|string',
            'driverData.driving_license_number' => 'required|string|unique:drivers,driving_license_number,' . ($this->driver?->id ?? 'NULL'),
            'driverData.driving_license_expiry_date' => 'required|date',
            'driverData.identity_card_expiry_date' => 'nullable|date',

            'driverData.driving_license_document_front' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'driverData.driving_license_document_back'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'driverData.identity_card_document_front'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'driverData.identity_card_document_back'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }

    public function save()
    {
        $this->validate();

        $fileKeys = array_keys($this->mediaCollectionsMap());
        $files = array_intersect_key($this->driverData, array_flip($fileKeys));
        $attributes = array_diff_key($this->driverData, array_flip($fileKeys));

        if ($this->driver->exists) {
            $this->driver->update($attributes);
        } else {
            $this->driver->fill($attributes);
            $this->driver->save();
        }

        foreach ($this->mediaCollectionsMap() as $key => $collection) {
            $this->attachMedia($files[$key] ?? null, $collection);
        }

        session()->flash('notify', 'Dane kierowcy zostały pomyślnie zaktualizowane.');

        return $this->redirect(route('drivers.index'), navigate: true);
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
            $result[$key] = $this->driver->getFirstMedia($collection)?->id;
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

    public function render()
    {
        return view('livewire.forms.drivers-form');
    }
}
