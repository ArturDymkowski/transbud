<?php

namespace App\Support\Planner;

/**
 * Which single resource type the Planner currently shows. Rows never mix types —
 * switching resets the driver/vehicle filter, since selected ids from one type are
 * meaningless for another.
 */
enum PlannerResourceType: string
{
    case DRIVER = 'driver';
    case TRACTOR = 'tractor';
    case TRAILER = 'trailer';

    public function label(): string
    {
        return match ($this) {
            self::DRIVER => __('deliveries.planner.resource_types.driver'),
            self::TRACTOR => __('deliveries.planner.resource_types.tractor'),
            self::TRAILER => __('deliveries.planner.resource_types.trailer'),
        };
    }

    /**
     * Label shown in the filter dropdown when nothing is selected (i.e. "all").
     * Drivers are people ("Wszyscy" in Polish), vehicles aren't ("Wszystkie") —
     * kept per-type so the grammar stays correct in both cases.
     */
    public function allLabel(): string
    {
        return match ($this) {
            self::DRIVER => __('deliveries.planner.all_drivers'),
            self::TRACTOR, self::TRAILER => __('labels.tables.all'),
        };
    }

    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
