<?php

namespace App\Livewire\Calendars;

use App\Livewire\Concerns\WithTransportSetEvents;
use App\Models\DeliveryTransportSet;
use Livewire\Component;

class DeliveriesCalendar extends Component
{
    use WithTransportSetEvents;

    public function mount(): void
    {
        $this->authorize('deliveries.view');
    }

    public function getEvents(string $start, string $end): array
    {
        return $this->transportSetEventsBetween($start, $end)
            ->get()
            ->map(fn (DeliveryTransportSet $transportSet) => [
                'id' => $transportSet->id,
                'title' => $this->transportSetEventTitle($transportSet),
                'start' => $transportSet->loading_at?->toIso8601String(),
                'end' => $transportSet->unloading_at?->toIso8601String(),
                'color' => $this->transportSetEventColor($transportSet),
                'allDay' => false,
            ])
            ->all();
    }

    public function openTransportSet(int $transportSetId, ?string $loadingAt = null, ?string $unloadingAt = null): void
    {
        $this->dispatch('open-transport-set-modal', transportSetId: $transportSetId, loadingAt: $loadingAt, unloadingAt: $unloadingAt);
    }

    public function render()
    {
        return view('livewire.calendars.deliveries-calendar');
    }
}
