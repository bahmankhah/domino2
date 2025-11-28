<?php

namespace App\Services;

use App\Models\OrderItem;

class OrderSnapshotService
{
    /**
     * call this when creating/updating an OrderItem to save current state.
     */
    public function fillSnapshots(OrderItem $item): void
    {
        if ($item->good_id && !$item->good_info) {
            $item->good_info = $item->good()->first()?->toArray();
        }
        if ($item->warehouse_id && !$item->warehouse_info) {
            $item->warehouse_info = $item->warehouse()->first()?->toArray();
        }
        if ($item->logistic_id && !$item->logistic_info) {
            $item->logistic_info = $item->logistic()->first()?->toArray();
        }
        if ($item->order_type_id && !$item->order_type_info) {
            $item->order_type_info = $item->orderType()->first()?->toArray();
        }
        // Referrer logic usually comes from the Order's creator or specific field
        // $item->referrer_info = ...
    }
}
