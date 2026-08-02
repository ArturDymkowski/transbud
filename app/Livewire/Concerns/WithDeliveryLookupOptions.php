<?php

namespace App\Livewire\Concerns;

use App\Models\Contractor;
use App\Models\ContractorAddress;
use App\Models\Driver;
use App\Models\Good;
use Livewire\Attributes\Computed;

trait WithDeliveryLookupOptions
{
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

    public function contractorAddressOptions(?int $contractorId): array
    {
        $options = ['' => __('labels.general.not_selected')];

        if (! $contractorId) {
            return $options;
        }

        foreach (ContractorAddress::where('contractor_id', $contractorId)->get() as $address) {
            $options[$address->id] = str_replace('<br>', ', ', $address->fullAddress);
        }

        return $options;
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
}
