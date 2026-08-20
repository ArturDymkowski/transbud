<?php

namespace App\Livewire\Planners;

use App\Livewire\Concerns\WithTransportSetEvents;
use App\Models\DeliveryTransportSet;
use App\Support\Planner\PlannerEvent;
use App\Support\Planner\Resources\DriverPlannerResourceProvider;
use App\Support\Planner\Resources\PlannerResourceProviderInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class DeliveriesPlanner extends Component
{
    use WithTransportSetEvents;

    private const START_HOUR = 0;

    private const END_HOUR = 24;

    public string $date;

    public int $pxPerHour = 80;

    /** @var array<int, int|string> selected resource ids; empty means "all" */
    public array $selectedResourceIds = [];

    public function mount(): void
    {
        $this->date = now()->toDateString();
    }

    private function resourceProvider(): PlannerResourceProviderInterface
    {
        return new DriverPlannerResourceProvider;
    }

    public function previousDay(): void
    {
        $this->date = Carbon::parse($this->date)->subDay()->toDateString();
    }

    public function nextDay(): void
    {
        $this->date = Carbon::parse($this->date)->addDay()->toDateString();
    }

    public function goToToday(): void
    {
        $this->date = now()->toDateString();
    }

    #[Computed]
    public function windowStart(): Carbon
    {
        return Carbon::parse($this->date)->startOfDay()->addHours(self::START_HOUR);
    }

    #[Computed]
    public function windowEnd(): Carbon
    {
        return Carbon::parse($this->date)->startOfDay()->addHours(self::END_HOUR);
    }

    /**
     * @return Collection<int, int> hour marks shown in the header (0..24)
     */
    #[Computed]
    public function hours(): Collection
    {
        return collect(range(self::START_HOUR, self::END_HOUR));
    }

    /**
     * All available resources, unfiltered — used to populate the filter dropdown
     * so unchecking one option never removes the others from the list.
     */
    #[Computed]
    public function allResources(): Collection
    {
        return $this->resourceProvider()->resources();
    }

    /**
     * @return Collection<int, string> resource id => label, for the filter dropdown
     */
    #[Computed]
    public function resourceOptions(): Collection
    {
        return $this->allResources()->mapWithKeys(fn ($resource) => [$resource->id => $resource->label]);
    }

    #[Computed]
    public function resources(): Collection
    {
        $resources = $this->allResources();

        if ($this->selectedResourceIds === []) {
            return $resources;
        }

        $selectedIds = collect($this->selectedResourceIds)->map(fn ($id) => (int) $id)->all();

        return $resources->whereIn('id', $selectedIds)->values();
    }

    /**
     * @return Collection<int, Collection<int, PlannerEvent>> events keyed by resource (driver) id
     */
    #[Computed]
    public function eventsByResource(): Collection
    {
        $windowStart = $this->windowStart();
        $windowEnd = $this->windowEnd();

        return $this->transportSetEventsBetween($windowStart, $windowEnd)
            ->whereNotNull('driver_id')
            ->get()
            ->map(fn (DeliveryTransportSet $transportSet) => PlannerEvent::forWindow(
                id: $transportSet->id,
                resourceId: $transportSet->driver_id,
                title: $this->transportSetEventTitle($transportSet),
                color: $this->transportSetEventColor($transportSet),
                startsAt: $transportSet->loading_at,
                endsAt: $transportSet->unloading_at ?? $transportSet->loading_at,
                windowStart: $windowStart,
                windowEnd: $windowEnd,
            ))
            ->groupBy('resourceId');
    }

    public function openTransportSet(int $transportSetId): void
    {
        $this->dispatch('open-transport-set-modal', transportSetId: $transportSetId);
    }

    #[On('transport-set-saved')]
    public function refresh(): void
    {
        unset($this->allResources, $this->resourceOptions, $this->resources, $this->eventsByResource);
    }

    public function render()
    {
        return view('livewire.planners.deliveries-planner');
    }
}
