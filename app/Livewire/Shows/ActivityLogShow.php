<?php

namespace App\Livewire\Shows;

use App\Livewire\Concerns\FormatsActivityLog;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class ActivityLogShow extends Component
{
    use FormatsActivityLog;

    public Activity $activity;

    public function mount(Activity $activity): void
    {
        abort_unless(auth()->user()?->is_super_admin, 403);

        $this->activity = $activity->loadMissing('causer');
    }

    public function description(): string
    {
        return __('activity_log.events.'.$this->activity->event).': '.$this->subjectLabel($this->activity);
    }

    public function render()
    {
        $changes = $this->activity->attribute_changes;

        return view('livewire.shows.activity-log-show', [
            'oldValues' => $changes?->get('old') ?? [],
            'newValues' => $changes?->get('attributes') ?? [],
        ]);
    }
}
