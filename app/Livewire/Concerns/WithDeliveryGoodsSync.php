<?php

namespace App\Livewire\Concerns;

use App\Models\DeliveryTransportSet;

trait WithDeliveryGoodsSync
{
    private function syncGoods(DeliveryTransportSet $transportSet, array $goodsData): void
    {
        $keepIds = [];

        foreach ($goodsData as $good) {
            $attributes = [
                'good_id' => $good['good_id'],
                'unit_id' => $good['unit_id'],
                'quantity' => $good['quantity'],
            ];

            if (! empty($good['id'])) {
                $transportSet->goods()->whereKey($good['id'])->update($attributes);
                $keepIds[] = $good['id'];
            } else {
                $keepIds[] = $transportSet->goods()->create($attributes)->id;
            }
        }

        $transportSet->goods()->whereNotIn('id', $keepIds)->delete();
    }
}
