<?php

namespace App\Livewire\Forms;

use App\Livewire\Concerns\WithSavedRedirect;
use App\Models\Good;
use App\Models\Unit;
use Livewire\Component;

class GoodsForm extends Component
{
    use WithSavedRedirect;

    public array $goodData = [];
    public ?Good $good = null;

    public function mount(?Good $good = null)
    {
        if ($good && $good->exists) {
            $this->good = $good;
        } else {
            $this->good = new Good();
        }

        $this->goodData = $this->good->only(['name', 'description', 'default_unit_id']);

        if (! $this->good->exists) {
            $this->goodData['default_unit_id'] = array_key_first($this->unitOptions);
        }
    }

    protected function rules(): array
    {
        return [
            'goodData.name' => 'required|string|max:255',
            'goodData.description' => 'nullable|string',
            'goodData.default_unit_id' => 'required|exists:units,id',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'goodData.name' => __('goods.name'),
            'goodData.description' => __('goods.description'),
            'goodData.default_unit_id' => __('goods.default_unit'),
        ];
    }

    public function getUnitOptionsProperty(): array
    {
        return Unit::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function save()
    {
        $this->validate();

        $isUpdate = $this->good->exists;

        if ($isUpdate) {
            $this->good->update($this->goodData);
        } else {
            $this->good->fill($this->goodData);
            $this->good->save();
        }

        return $this->flashSavedAndRedirect($isUpdate, 'goods.index');
    }

    public function render()
    {
        return view('livewire.forms.goods-form');
    }
}
