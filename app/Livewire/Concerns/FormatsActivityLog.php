<?php

namespace App\Livewire\Concerns;

use App\Models\User;
use Spatie\Activitylog\Models\Activity;

trait FormatsActivityLog
{
    public function subjectLabel(Activity $activity): string
    {
        $resource = $activity->log_name ? __('activity_log.resources.'.$activity->log_name) : '?';

        return $resource.' #'.($activity->subject_id ?? '?');
    }

    public function causerLabel(Activity $activity): string
    {
        return $activity->causer instanceof User ? $activity->causer->name : __('activity_log.system');
    }

    public function formatChangeValue(mixed $value): string
    {
        if (is_null($value)) {
            return '-';
        }

        if (is_bool($value)) {
            return $value ? __('labels.tables.yes') : __('labels.tables.no');
        }

        if (is_array($value)) {
            return implode(', ', $value);
        }

        return (string) $value;
    }

    public function eventColor(string $event): string
    {
        return match ($event) {
            'created' => '#12b76a',
            'updated' => '#0ba5ec',
            'deleted' => '#f04438',
            'restored' => '#f79009',
            default => '#667085',
        };
    }
}
