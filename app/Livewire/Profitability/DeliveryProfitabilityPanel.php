<?php

namespace App\Livewire\Profitability;

use App\Enums\DeliveryCostTypeEnum;
use App\Models\Delivery;
use App\Support\Profitability\DeliveryProfitability;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Computed;
use Livewire\Component;

class DeliveryProfitabilityPanel extends Component
{
    public Delivery $delivery;

    public array $costData = [];

    public ?int $editingCostId = null;

    public bool $showCostModal = false;

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

    public function getCostTypeOptionsProperty(): array
    {
        return DeliveryCostTypeEnum::getOptions();
    }

    public function getTransportSetOptionsProperty(): array
    {
        return ['' => __('deliveries.cost.whole_delivery')] + $this->delivery->transportSets
            ->mapWithKeys(fn ($transportSet) => [
                $transportSet->id => collect([
                    $transportSet->driver->name ?? null,
                    $transportSet->vehicle->registration_number ?? null,
                    $transportSet->trailer->registration_number ?? null,
                ])->filter()->implode(' / ') ?: ('#'.$transportSet->id),
            ])->all();
    }

    public function openCreateCostModal(?int $transportSetId = null): void
    {
        $this->authorize('deliveries.edit');

        $this->editingCostId = null;
        $this->costData = [
            'type' => DeliveryCostTypeEnum::FUEL->value,
            'amount' => '',
            'description' => '',
            'delivery_transport_set_id' => $transportSetId,
        ];

        $this->resetValidation();
        $this->showCostModal = true;
    }

    public function openEditCostModal(int $costId): void
    {
        $this->authorize('deliveries.edit');

        $cost = $this->delivery->costs->firstWhere('id', $costId);
        abort_unless($cost, 404);

        $this->editingCostId = $cost->id;
        $this->costData = [
            'type' => $cost->type->value,
            'amount' => number_format($cost->amount / 100, 2, '.', ''),
            'description' => $cost->description ?? '',
            'delivery_transport_set_id' => $cost->delivery_transport_set_id,
        ];

        $this->resetValidation();
        $this->showCostModal = true;
    }

    public function closeCostModal(): void
    {
        $this->showCostModal = false;
        $this->reset('editingCostId', 'costData');
    }

    protected function rules(): array
    {
        return [
            'costData.type' => ['required', new Enum(DeliveryCostTypeEnum::class)],
            'costData.amount' => 'required|numeric|min:0',
            'costData.description' => 'nullable|string|max:255',
            'costData.delivery_transport_set_id' => [
                'nullable',
                Rule::exists('delivery_transport_sets', 'id')->where('delivery_id', $this->delivery->id),
            ],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'costData.type' => __('deliveries.cost.type'),
            'costData.amount' => __('deliveries.cost.amount'),
            'costData.description' => __('deliveries.cost.description'),
            'costData.delivery_transport_set_id' => __('deliveries.transport_set.transport_set'),
        ];
    }

    public function saveCost(): void
    {
        $this->authorize('deliveries.edit');

        $validated = $this->validate()['costData'];

        // Currency is not a form field: a cost always inherits the delivery's currency,
        // so a mismatch is structurally impossible instead of merely validated against.
        $attributes = [
            'type' => $validated['type'],
            'amount' => (int) round(((float) $validated['amount']) * 100),
            'currency' => $this->delivery->currency->value,
            'description' => $validated['description'] ?: null,
            'delivery_transport_set_id' => $validated['delivery_transport_set_id'] ?: null,
        ];

        if ($this->editingCostId) {
            $this->delivery->costs()->whereKey($this->editingCostId)->update($attributes);
        } else {
            $this->delivery->costs()->create($attributes);
        }

        $this->loadDelivery();
        $this->closeCostModal();

        $this->dispatch('notify', message: __('labels.general.saved_success'));
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
