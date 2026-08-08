<?php

namespace App\Http\Controllers;

use App\Helpers\ExpiryHelper;
use App\Models\Driver;
use App\Models\Vehicle;

class DashboardController extends Controller
{
    public function index()
    {
        $entries = collect();

        if (auth()->user()?->can('drivers.view')) {
            $entries = $entries->merge($this->driverEntries());
        }

        if (auth()->user()?->can('vehicles.view')) {
            $entries = $entries->merge($this->vehicleEntries());
        }

        $grouped = [
            ExpiryHelper::RED => [],
            ExpiryHelper::YELLOW => [],
            ExpiryHelper::GREEN => [],
        ];

        foreach ($entries as $entry) {
            $status = ExpiryHelper::status($entry['date']);

            if ($status === null) {
                continue;
            }

            $entry['days'] = ExpiryHelper::daysRemaining($entry['date']);
            $grouped[$status][] = $entry;
        }

        foreach ($grouped as &$group) {
            usort($group, fn ($a, $b) => $a['days'] <=> $b['days']);
        }
        unset($group);

        return view('pages.dashboard.index', compact('grouped'));
    }

    private function driverEntries(): array
    {
        $entries = [];

        $fields = [
            'driving_license_expiry_date' => __('drivers.driving_license_expiry_date'),
            'identity_card_expiry_date' => __('drivers.identity_card_expiry_date'),
        ];

        $drivers = Driver::query()
            ->select(['id', 'name', ...array_keys($fields)])
            ->get();

        foreach ($drivers as $driver) {
            foreach ($fields as $field => $label) {
                $entries[] = [
                    'subject' => $driver->name,
                    'label' => $label,
                    'date' => $driver->{$field},
                    'route' => route('drivers.edit', $driver->id),
                ];
            }
        }

        return $entries;
    }

    private function vehicleEntries(): array
    {
        $entries = [];

        $fields = [
            'technical_inspection_expiry_date' => __('vehicles.technical_inspection_expiry_date'),
            'insurance_expiry_date' => __('vehicles.insurance_expiry_date'),
            'tachograph_inspection_expiry_date' => __('vehicles.tachograph_inspection_expiry_date'),
        ];

        $vehicles = Vehicle::query()
            ->select(['id', 'registration_number', ...array_keys($fields)])
            ->get();

        foreach ($vehicles as $vehicle) {
            foreach ($fields as $field => $label) {
                $entries[] = [
                    'subject' => $vehicle->registration_number,
                    'label' => $label,
                    'date' => $vehicle->{$field},
                    'route' => route('vehicles.edit', $vehicle->id),
                ];
            }
        }

        return $entries;
    }
}
