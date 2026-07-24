<?php

namespace App\Livewire\Tables;

use App\Enums\CountriesEnum;
use App\Livewire\Concerns\WithBulkSelection;
use App\Livewire\Concerns\WithFilters;
use App\Livewire\Concerns\WithPerPage;
use App\Livewire\Concerns\WithTableSorting;
use App\Models\Contractor;
use App\Models\ContractorAddress;
use Illuminate\Validation\Rules\Enum;
use Livewire\Component;
use Livewire\WithPagination;

class ContractorAddressesTable extends Component
{
    use WithPagination, WithTableSorting, WithPerPage, WithBulkSelection, WithFilters;

    public array $allowedSortFields = ['id', 'contractor_name', 'is_active'];

    public ?Contractor $contractor = null;

    public string $search = '';
    public string $isActive = '';
    public ?int $country = null;
    public string $trashed = '';

    public bool $showCreateModal = false;
    public array $createAddressData = [];

    public function mount(?Contractor $contractor = null): void
    {
        $this->contractor = ($contractor && $contractor->exists) ? $contractor : null;
    }

    protected function filterFields(): array
    {
        return ['search', 'isActive', 'country', 'trashed'];
    }

    public function render()
    {
        $query = ContractorAddress::with('contractor')
            ->when($this->contractor, fn ($q) => $q->where('contractor_id', $this->contractor->id))
            ->search($this->search)
            ->when(filled($this->isActive), fn ($q) => $q->where('is_active', $this->isActive))
            ->when(filled($this->country), fn ($q) => $q->where('country', $this->country))
            ->when($this->trashed === 'with', fn ($q) => $q->withTrashed())
            ->when($this->trashed === 'only', fn ($q) => $q->onlyTrashed());

        if ($this->sortField === 'contractor_name') {
            $query->join('contractors', 'contractors.id', '=', 'contractor_addresses.contractor_id')
                ->orderBy('contractors.name', $this->sortDirection)
                ->select('contractor_addresses.*');
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $addresses = $query->paginate($this->perPage);

        $this->idsOnPage = $addresses->pluck('id')->toArray();

        return view('livewire.tables.contractor-addresses-table', [
            'addresses' => $addresses,
        ]);
    }

    public function deleteSelected(): void
    {
        $this->deleteSelectedRecords(ContractorAddress::class);
    }

    public function deleteAddress(int $id): void
    {
        ContractorAddress::where('id', $id)->delete();
        $this->dispatch('notify', message: __('labels.general.deleted_success'));
    }

    public function toggleActive(int $addressId): void
    {
        $address = ContractorAddress::findOrFail($addressId);
        $address->is_active = ! $address->is_active;
        $address->save();

        $this->dispatch('notify', message: __('labels.general.updated_success'));
    }

    public function openCreateModal(): void
    {
        $this->createAddressData = [
            'country' => null,
            'zipcode' => '',
            'city' => '',
            'street' => '',
            'house_nr' => '',
            'apartment_nr' => '',
        ];
        $this->resetValidation();
        $this->showCreateModal = true;
    }

    public function createAddress(): void
    {
        if (! $this->contractor) {
            return;
        }

        $validated = $this->validate([
            'createAddressData.country' => ['required', new Enum(CountriesEnum::class)],
            'createAddressData.zipcode' => 'required|string|max:20',
            'createAddressData.city' => 'required|string|max:100',
            'createAddressData.street' => 'required|string|max:100',
            'createAddressData.house_nr' => 'nullable|string|max:20',
            'createAddressData.apartment_nr' => 'nullable|string|max:20',
        ], [], [
            'createAddressData.country' => __('labels.address.country'),
            'createAddressData.zipcode' => __('labels.address.zipcode'),
            'createAddressData.city' => __('labels.address.city'),
            'createAddressData.street' => __('labels.address.street'),
            'createAddressData.house_nr' => __('labels.address.house_nr'),
            'createAddressData.apartment_nr' => __('labels.address.apartment_nr'),
        ]);

        ContractorAddress::create([
            ...$validated['createAddressData'],
            'contractor_id' => $this->contractor->id,
        ]);

        $this->showCreateModal = false;
        $this->reset('createAddressData');

        $this->dispatch('notify', message: __('labels.general.saved_success'));
    }

    public function getCountryOptionsProperty(): array
    {
        return ['' => __('labels.tables.all')] + CountriesEnum::getOptions();
    }

    public function getTrashedOptionsProperty(): array
    {
        return [
            '' => __('labels.tables.without_trashed'),
            'with' => __('labels.tables.with_trashed'),
            'only' => __('labels.tables.only_trashed'),
        ];
    }

    public function getActiveFiltersProperty(): array
    {
        $filters = [];

        if (filled($this->search)) {
            $filters[] = [
                'label' => __('labels.tables.search') . ': "' . $this->search . '"',
                'property' => 'search',
            ];
        }

        if (filled($this->isActive)) {
            $filters[] = [
                'label' => __('labels.tables.active') . ': ' . ($this->isActive === '1'
                        ? __('labels.tables.yes')
                        : __('labels.tables.no')),
                'property' => 'isActive',
            ];
        }

        if (filled($this->country)) {
            $filters[] = [
                'label' => __('labels.address.country') . ': ' . CountriesEnum::fromId($this->country)->label(),
                'property' => 'country',
            ];
        }

        if (filled($this->trashed)) {
            $filters[] = [
                'label' => $this->trashedOptions[$this->trashed],
                'property' => 'trashed',
            ];
        }

        return $filters;
    }
}
