<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait WithDemoLimits
{
    /**
     * Call this only from the "create" branch of a form's save() — editing
     * existing demo data is intentionally not limited by this.
     */
    protected function ensureDemoRecordLimitsAllow(string $modelClass): void
    {
        $this->ensureDemoRecordCountIsNotExceeded($modelClass);
        $this->ensureDemoRecordCreationIsNotRateLimited($modelClass);
    }

    /**
     * Call this before attaching an uploaded file to a media collection,
     * regardless of whether the owning record is being created or edited — disk
     * usage grows the same way either way.
     */
    protected function ensureDemoDiskHasRoomFor(string $disk, TemporaryUploadedFile $file): void
    {
        $maxBytes = config('demo.max_disk_bytes');
        $currentBytes = (int) Media::query()->where('disk', $disk)->sum('size');

        if ($currentBytes + $file->getSize() > $maxBytes) {
            throw ValidationException::withMessages([
                'demoLimit' => trans('demo.disk_limit_reached'),
            ]);
        }
    }

    private function ensureDemoRecordCountIsNotExceeded(string $modelClass): void
    {
        $maxRecords = config('demo.max_records');

        if ($modelClass::count() >= $maxRecords) {
            throw ValidationException::withMessages([
                'demoLimit' => trans('demo.record_limit_reached', ['max' => $maxRecords]),
            ]);
        }
    }

    private function ensureDemoRecordCreationIsNotRateLimited(string $modelClass): void
    {
        $key = 'demo-create:'.class_basename($modelClass).':'.request()->ip();
        $maxAttempts = config('demo.record_creation.max_attempts');

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            throw ValidationException::withMessages([
                'demoLimit' => trans('demo.rate_limited', ['seconds' => RateLimiter::availableIn($key)]),
            ]);
        }

        RateLimiter::hit($key, config('demo.record_creation.decay_minutes') * 60);
    }
}
