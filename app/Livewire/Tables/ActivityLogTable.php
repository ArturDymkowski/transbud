<?php

namespace App\Livewire\Tables;

use App\Livewire\Concerns\FormatsActivityLog;
use App\Livewire\Concerns\WithFilters;
use App\Livewire\Concerns\WithPerPage;
use App\Livewire\Concerns\WithTableSorting;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

/**
 * @property-read array $logNameOptions
 * @property-read array $eventOptions
 */
class ActivityLogTable extends Component
{
    use FormatsActivityLog, WithFilters, WithPagination, WithPerPage, WithTableSorting;

    public string $search = '';

    public string $logName = '';

    public string $event = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->is_super_admin, 403);
    }

    protected function filterFields(): array
    {
        return ['search', 'logName', 'event'];
    }

    public function render()
    {
        $activities = Activity::query()
            ->with('causer')
            ->when($this->logName, fn ($q) => $q->where('log_name', $this->logName))
            ->when($this->event, fn ($q) => $q->where('event', $this->event))
            ->when(filled($this->search), fn ($q) => $q->whereHasMorph('causer', [User::class], function ($q2) {
                $q2->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            }))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.tables.activity-log-table', [
            'activities' => $activities,
        ]);
    }

    public function getLogNameOptionsProperty(): array
    {
        return ['' => __('labels.tables.all')] + __('activity_log.resources');
    }

    public function getEventOptionsProperty(): array
    {
        return ['' => __('labels.tables.all')] + __('activity_log.events');
    }

    public function getActiveFiltersProperty(): array
    {
        $filters = [];

        if (filled($this->search)) {
            $filters[] = [
                'label' => __('labels.tables.search').': "'.$this->search.'"',
                'property' => 'search',
            ];
        }

        if (filled($this->logName)) {
            $filters[] = [
                'label' => __('activity_log.resource').': '.$this->logNameOptions[$this->logName],
                'property' => 'logName',
            ];
        }

        if (filled($this->event)) {
            $filters[] = [
                'label' => __('activity_log.event').': '.$this->eventOptions[$this->event],
                'property' => 'event',
            ];
        }

        return $filters;
    }
}
