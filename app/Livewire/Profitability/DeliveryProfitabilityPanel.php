<?php

namespace App\Livewire\Profitability;

use App\Models\Delivery;
use App\Support\Profitability\DeliveryProfitability;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class DeliveryProfitabilityPanel extends Component
{
    public Delivery $delivery;

    public function mount(Delivery $delivery): void
    {
        $this->authorize('deliveries.view');

        $this->delivery = $delivery;
        $this->loadDelivery();
    }

    private function loadDelivery(): void
    {
        $this->delivery->load([
            'costs.transportSet.driver',
            'costs.transportSet.vehicle',
            'costs.transportSet.trailer',
            'transportSets.driver',
            'transportSets.vehicle',
            'transportSets.trailer',
        ]);
    }

    #[Computed]
    public function profitability(): DeliveryProfitability
    {
        return DeliveryProfitability::fromDelivery($this->delivery);
    }

    // The cost add/edit form lives in the separate DeliveryCostModal component
    // (embedded below in the view). These two just relay the button clicks to
    // it, since sibling Livewire components can't call each other's methods
    // directly - only dispatch/listen to browser events.
    public function openCreateCostModal(?int $transportSetId = null): void
    {
        $this->dispatch('open-create-cost-modal', transportSetId: $transportSetId);
    }

    public function openEditCostModal(int $costId): void
    {
        $this->dispatch('open-edit-cost-modal', costId: $costId);
    }

    #[On('cost-saved')]
    public function refreshAfterCostSaved(): void
    {
        $this->loadDelivery();
    }

    public function deleteCost(int $costId): void
    {
        $this->authorize('deliveries.edit');

        $this->delivery->costs()->whereKey($costId)->delete();

        $this->loadDelivery();

        $this->dispatch('notify', message: __('labels.general.deleted_success'));
    }

    public function render()
    {
        return view('livewire.profitability.delivery-profitability-panel');
    }
}
