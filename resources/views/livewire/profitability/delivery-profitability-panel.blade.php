@php($profitability = $this->profitability)

<div class="flex flex-col gap-6">

    @if($editable)
        <livewire:modals.delivery-cost-modal :delivery="$delivery"/>
    @endif

    <!-- Sekcja: Podsumowanie -->
    <x-form.section title="{{ __('deliveries.profitability.summary') }}">
        @if($delivery->freight_amount === null)
            <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">{{ __('deliveries.profitability.no_freight_amount') }}</p>
        @endif

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('deliveries.profitability.revenue') }}</p>
                <p class="mt-1 text-lg font-semibold text-gray-800 dark:text-white/90">
                    {{ \App\Helpers\MoneyHelper::format($profitability->revenueAmount, $profitability->currency) }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('deliveries.profitability.costs') }}</p>
                <p class="mt-1 text-lg font-semibold text-gray-800 dark:text-white/90">
                    {{ \App\Helpers\MoneyHelper::format($profitability->totalCostAmount, $profitability->currency) }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('deliveries.profitability.profit') }}</p>
                <p class="mt-1 text-lg font-semibold text-gray-800 dark:text-white/90">
                    {{ \App\Helpers\MoneyHelper::format($profitability->profitAmount, $profitability->currency) }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('deliveries.profitability.margin') }}</p>
                <p class="mt-1">
                    <x-ui.status-badge :color="$profitability->marginColor()">
                        {{ $profitability->marginPercent !== null ? number_format($profitability->marginPercent, 2, ',', ' ').'%' : '-' }}
                    </x-ui.status-badge>
                </p>
            </div>
        </div>
    </x-form.section>

    <!-- Sekcja: Koszty -->
    <x-form.section title="{{ __('deliveries.cost.costs') }}">
        <div class="flex flex-col gap-4">
            @foreach($profitability->transportSetBreakdowns as $breakdown)
                <div wire:key="cost-breakdown-set-{{ $breakdown->transportSet->id }}" class="flex flex-col gap-3 p-4 rounded-xl border border-gray-100 dark:border-gray-800">
                    <div class="flex items-center justify-between">
                        <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $breakdown->label() }}</h5>
                        @if($editable)
                            <x-ui.button wire:click="openCreateCostModal({{ $breakdown->transportSet->id }})" size="sm" variant="outline">
                                {{ __('deliveries.cost.add') }}
                            </x-ui.button>
                        @endif
                    </div>

                    <table class="min-w-full max-md:block">
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 max-md:block max-md:divide-y-0 max-md:space-y-4">
                        @forelse($breakdown->costs as $cost)
                            <tr wire:key="cost-row-{{ $cost->id }}" class="max-md:block max-md:space-y-3 max-md:rounded-xl max-md:border max-md:border-gray-200 max-md:p-4 max-md:divide-y max-md:divide-gray-100 dark:max-md:border-gray-700 dark:max-md:divide-gray-800">
                                <x-tables.td :label="__('deliveries.cost.type')">{{ $cost->type->label() }}</x-tables.td>
                                <x-tables.td :label="__('deliveries.cost.description')">{{ $cost->description ?? '-' }}</x-tables.td>
                                <x-tables.td :label="__('deliveries.cost.amount')" class="text-right">{{ \App\Helpers\MoneyHelper::format($cost->amount, $profitability->currency) }}</x-tables.td>
                                <x-tables.td class="flex justify-end gap-2">
                                    @if($editable)
                                        <button type="button" wire:click="openEditCostModal({{ $cost->id }})" class="text-gray-400 hover:text-brand-500 dark:text-gray-500">
                                            <x-heroicon-o-pencil-square class="w-5 h-5"/>
                                        </button>
                                        <x-tables.action-delete wire:click="deleteCost({{ $cost->id }})" :confirm="__('deliveries.cost.confirm_delete')" :label="__('deliveries.cost.remove')">
                                            <x-heroicon-o-trash class="w-5 h-5 hover:text-red-500"/>
                                        </x-tables.action-delete>
                                    @endif
                                </x-tables.td>
                            </tr>
                        @empty
                            <tr class="max-md:block">
                                <x-tables.td>{{ __('deliveries.cost.empty') }}</x-tables.td>
                            </tr>
                        @endforelse
                        </tbody>
                        @if($breakdown->costs->isNotEmpty())
                            <tfoot>
                            <tr class="max-md:flex max-md:items-center max-md:justify-between max-md:px-1 max-md:py-3 border-gray-200 border-t font-semibold dark:border-gray-700">
                                <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300 max-md:p-0" colspan="2">{{ __('deliveries.profitability.total') }}</td>
                                <td class="px-4 py-4 text-sm text-right text-gray-700 dark:text-gray-300 max-md:p-0" colspan="2">{{ \App\Helpers\MoneyHelper::format($breakdown->totalAmount, $profitability->currency) }}</td>
                            </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            @endforeach

            <!-- Koszty pozostałe (bez przypisanego zestawu) -->
            <div class="flex flex-col gap-3 p-4 rounded-xl border border-gray-100 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('deliveries.cost.remaining_costs') }}</h5>
                    @if($editable)
                        <x-ui.button wire:click="openCreateCostModal" size="sm" variant="outline">
                            {{ __('deliveries.cost.add') }}
                        </x-ui.button>
                    @endif
                </div>

                <table class="min-w-full max-md:block">
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 max-md:block max-md:divide-y-0 max-md:space-y-4">
                    @forelse($profitability->directCosts as $cost)
                        <tr wire:key="cost-row-{{ $cost->id }}" class="max-md:block max-md:space-y-3 max-md:rounded-xl max-md:border max-md:border-gray-200 max-md:p-4 max-md:divide-y max-md:divide-gray-100 dark:max-md:border-gray-700 dark:max-md:divide-gray-800">
                            <x-tables.td :label="__('deliveries.cost.type')">{{ $cost->type->label() }}</x-tables.td>
                            <x-tables.td :label="__('deliveries.cost.description')">{{ $cost->description ?? '-' }}</x-tables.td>
                            <x-tables.td :label="__('deliveries.cost.amount')" class="text-right">{{ \App\Helpers\MoneyHelper::format($cost->amount, $profitability->currency) }}</x-tables.td>
                            <x-tables.td class="flex justify-end gap-2">
                                @if($editable)
                                    <button type="button" wire:click="openEditCostModal({{ $cost->id }})" class="text-gray-400 hover:text-brand-500 dark:text-gray-500">
                                        <x-heroicon-o-pencil-square class="w-5 h-5"/>
                                    </button>
                                    <x-tables.action-delete wire:click="deleteCost({{ $cost->id }})" :confirm="__('deliveries.cost.confirm_delete')" :label="__('deliveries.cost.remove')">
                                        <x-heroicon-o-trash class="w-5 h-5 hover:text-red-500"/>
                                    </x-tables.action-delete>
                                @endif
                            </x-tables.td>
                        </tr>
                    @empty
                        <tr class="max-md:block">
                            <x-tables.td>{{ __('deliveries.cost.empty') }}</x-tables.td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-white/5">
                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('deliveries.profitability.total_costs') }}</span>
                <span class="font-semibold text-gray-800 dark:text-white/90">{{ \App\Helpers\MoneyHelper::format($profitability->totalCostAmount, $profitability->currency) }}</span>
            </div>
        </div>
    </x-form.section>

</div>
