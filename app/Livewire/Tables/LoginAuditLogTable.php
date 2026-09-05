<?php

namespace App\Livewire\Tables;

use App\Livewire\Concerns\WithFilters;
use App\Livewire\Concerns\WithPerPage;
use App\Livewire\Concerns\WithTableSorting;
use App\Models\LoginAuditLog;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Read-only listing — no create/edit/delete on purpose, an audit trail shouldn't be
 * editable through the app it's auditing. Access is restricted to
 * `is_super_admin` users; see App\Http\Middleware\EnsureSuperAdmin (route) and the
 * `mount()` check below (Livewire's own AJAX endpoint doesn't go through route
 * middleware on re-renders, same reasoning as the Show/Form components).
 *
 * @property-read array $successfulOptions
 */
class LoginAuditLogTable extends Component
{
    use WithFilters, WithPagination, WithPerPage, WithTableSorting;

    public string $search = '';

    public string $successful = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->is_super_admin, 403);
    }

    protected function filterFields(): array
    {
        return ['search', 'successful'];
    }

    public function render()
    {
        $logs = LoginAuditLog::query()
            ->when(filled($this->search), fn ($q) => $q->where('email', 'like', '%'.$this->search.'%'))
            ->when(filled($this->successful), fn ($q) => $q->where('successful', $this->successful))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.tables.login-audit-log-table', [
            'logs' => $logs,
        ]);
    }

    public function getSuccessfulOptionsProperty(): array
    {
        return [
            '' => __('labels.tables.all'),
            '1' => __('login_audit_log.successful'),
            '0' => __('login_audit_log.failed'),
        ];
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

        if (filled($this->successful)) {
            $filters[] = [
                'label' => __('login_audit_log.status').': '.$this->successfulOptions[$this->successful],
                'property' => 'successful',
            ];
        }

        return $filters;
    }
}
