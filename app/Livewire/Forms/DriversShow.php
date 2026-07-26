<?php

namespace App\Livewire\Forms;

use App\Enums\VehicleTypeEnum;
use App\Models\Driver;
use App\Models\Vehicle;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DriversShow extends Component
{
    public Driver $driver;

    public array $driverData = [];

    public function mount(Driver $driver)
    {
        $this->driver = $driver;

        $this->driverData = $this->driver->only([
            'name', 'phone', 'pesel', 'country', 'zipcode',
            'city', 'street', 'house_nr', 'apartment_nr', 'extra_info',
            'driving_license_number', 'driving_license_expiry_date',
            'identity_card_number', 'identity_card_expiry_date',
        ]);

        $this->driverData['tractor_id'] = $this->driver->vehicles()->where('type', VehicleTypeEnum::TRACTOR->value)->value('vehicles.id');
        $this->driverData['trailer_id'] = $this->driver->vehicles()->where('type', VehicleTypeEnum::TRAILER->value)->value('vehicles.id');
    }

    #[Computed]
    public function tractorOptions(): array
    {
        return $this->vehicleOptions(VehicleTypeEnum::TRACTOR);
    }

    #[Computed]
    public function trailerOptions(): array
    {
        return $this->vehicleOptions(VehicleTypeEnum::TRAILER);
    }

    private function vehicleOptions(VehicleTypeEnum $type): array
    {
        $options = ['' => __('labels.general.not_selected')];

        foreach (Vehicle::where('type', $type->value)->orderBy('registration_number')->get() as $vehicle) {
            $options[$vehicle->id] = $vehicle->registration_number;
        }

        return $options;
    }

    #[Computed]
    public function existingMedia(): array
    {
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

    public function downloadDocument(string $key): ?BinaryFileResponse
    {
        $collectionsMap = $this->mediaCollectionsMap();

        if (! isset($collectionsMap[$key])) {
            return null;
        }

        $media = $this->driver->getFirstMedia($collectionsMap[$key]);

        if (! $media) {
            return null;
        }

        return response()->download($media->getPath(), $media->file_name);
    }

    private function mediaCollectionsMap(): array
    {
        return [
            'driving_license_document_front' => Driver::MEDIA_DRIVING_LICENSE_FRONT,
            'driving_license_document_back' => Driver::MEDIA_DRIVING_LICENSE_BACK,
            'identity_card_document_front' => Driver::MEDIA_IDENTITY_CARD_FRONT,
            'identity_card_document_back' => Driver::MEDIA_IDENTITY_CARD_BACK,
        ];
    }

    public function render()
    {
        return view('livewire.forms.drivers-show');
    }
}
