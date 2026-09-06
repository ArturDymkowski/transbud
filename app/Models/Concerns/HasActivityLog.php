<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

trait HasActivityLog
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        $options = LogOptions::defaults()->useLogName($this->activityLogName());

        $options = $this->activityLogAttributes() !== null
            ? $options->logOnly($this->activityLogAttributes())
            : $options->logFillable();

        return $options
            ->logExcept($this->activityLogExcept())
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected function activityLogName(): string
    {
        return Str::kebab(Str::pluralStudly(class_basename($this)));
    }

    protected function activityLogAttributes(): ?array
    {
        return null;
    }

    protected function activityLogExcept(): array
    {
        return [];
    }
}
