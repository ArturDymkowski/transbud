<?php

namespace App\Enums;

enum DeliveryStatusEnum: int
{
    case PLANNED = 0; // brak przypisanych zestawów transportowych
    case ASSIGNED = 1; // wszystkie przypisane
    case IN_PROGRESS = 2; // chociaż jeden w trasie
    case COMPLETED = 3; // wszystkie dostarczone
    case CANCELLED = 4; // anulowane

    public function label(): string
    {
        return match ($this) {
            self::PLANNED => __('deliveries.status.planned'),
            self::ASSIGNED => __('deliveries.status.assigned'),
            self::IN_PROGRESS => __('deliveries.status.in_progress'),
            self::COMPLETED => __('deliveries.status.completed'),
            self::CANCELLED => __('deliveries.status.cancelled'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PLANNED => 'gray',
            self::ASSIGNED => 'blue',
            self::IN_PROGRESS => 'yellow',
            self::COMPLETED => 'green',
            self::CANCELLED => 'red',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this->color()) {
            'gray' => 'bg-gray-100 text-gray-700 dark:bg-white/5 dark:text-gray-400',
            'blue' => 'bg-blue-light-100 text-blue-light-700 dark:bg-blue-light-500/15 dark:text-blue-light-400',
            'yellow' => 'bg-warning-100 text-warning-700 dark:bg-warning-500/15 dark:text-warning-400',
            'green' => 'bg-success-100 text-success-700 dark:bg-success-500/15 dark:text-success-400',
            'red' => 'bg-error-100 text-error-700 dark:bg-error-500/15 dark:text-error-400',
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
