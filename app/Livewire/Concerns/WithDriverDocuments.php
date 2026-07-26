<?php

namespace App\Livewire\Concerns;

use App\Models\Driver;
use Livewire\Attributes\Computed;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

trait WithDriverDocuments
{
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

    public function downloadDocument(string $key): ?BinaryFileResponse
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

    private function mediaCollectionsMap(): array
    {
        return [
            'driving_license_document_front' => Driver::MEDIA_DRIVING_LICENSE_FRONT,
            'driving_license_document_back' => Driver::MEDIA_DRIVING_LICENSE_BACK,
            'identity_card_document_front' => Driver::MEDIA_IDENTITY_CARD_FRONT,
            'identity_card_document_back' => Driver::MEDIA_IDENTITY_CARD_BACK,
        ];
    }
}
