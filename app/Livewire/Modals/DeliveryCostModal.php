<?php

namespace App\Livewire\Modals;

use App\Enums\DeliveryCostTypeEnum;
use App\Models\Delivery;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Add/edit modal for a single DeliveryCost, embedded once inside
 * DeliveryProfitabilityPanel. The panel's buttons don't call this component
 * directly (Livewire components can't call each other's methods); instead the
 * panel dispatches `open-create-cost-modal`/`open-edit-cost-modal` and this
 * modal dispatches `cost-saved` back so the panel can refresh its totals.
 */
class DeliveryCostModal extends Component
{
    public Delivery $delivery;

    public array $costData = [];

    public ?int $editingCostId = null;

    public bool $isOpen = false;

    public function mount(Delivery $delivery): void
    {
        $this->delivery = $delivery;
        $this->delivery->load(['costs', 'transportSets.driver', 'transportSets.vehicle', 'transportSets.trailer']);
        $this->costData = $this->emptyCostData();
    }

    private function emptyCostData(?int $transportSetId = null): array
    {
        return [
            'type' => DeliveryCostTypeEnum::FUEL->value,
            'amount' => '',
            'description' => '',
            'delivery_transport_set_id' => $transportSetId,
        ];
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
                    $transportSet->driver?->name,
                    $transportSet->vehicle?->registration_number,
                    $transportSet->trailer?->registration_number,
                ])->filter()->implode(' / ') ?: ('#'.$transportSet->id),
            ])->all();
    }

    #[On('open-create-cost-modal')]
    public function openCreateCostModal(?int $transportSetId = null): void
    {
        $this->authorize('deliveries.edit');

        $this->editingCostId = null;
        $this->costData = $this->emptyCostData($transportSetId);

        $this->resetValidation();
        $this->isOpen = true;
    }

    #[On('open-edit-cost-modal')]
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
        $this->isOpen = true;
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->editingCostId = null;
        $this->costData = $this->emptyCostData();
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

        if (isset($this->costData['amount'])) {
            $this->costData['amount'] = str_replace(',', '.', (string) $this->costData['amount']);
        }

        $validated = $this->validate()['costData'];

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

        $this->delivery->load('costs');
        $this->closeModal();

        $this->dispatch('notify', message: __('labels.general.saved_success'));
        $this->dispatch('cost-saved');
    }

    public function render()
    {
        return view('livewire.modals.delivery-cost-modal');
    }
}
