<?php

namespace App\Observers;

use App\Models\OrderItem;
use App\Services\OrderSnapshotService;
use App\Services\FinancialService;

class OrderItemObserver
{
    protected $snapshotService;
    protected $financialService;

    public function __construct(OrderSnapshotService $snapshotService, FinancialService $financialService)
    {
        $this->snapshotService = $snapshotService;
        $this->financialService = $financialService;
    }

    public function creating(OrderItem $orderItem): void
    {
        $this->snapshotService->fillSnapshots($orderItem);
    }

    public function updating(OrderItem $orderItem): void
    {
        $this->snapshotService->fillSnapshots($orderItem);
    }

    public function saved(OrderItem $orderItem): void
    {
        // Make the good unavailable when order item is created/saved
        if ($orderItem->good && $orderItem->wasRecentlyCreated) {
            $orderItem->good->update(['is_available' => false]);
        }
        
        // Recalculate incomes whenever the item is saved (created or edited)
        $this->financialService->calculateItemIncomes($orderItem);
    }
    
    public function deleted(OrderItem $orderItem): void
    {
        // Make the good available again when order item is deleted
        if ($orderItem->good) {
            $orderItem->good->update(['is_available' => true]);
        }
        
        // Incomes cascade delete usually via DB foreign keys, 
        // but if soft deletes or strict logic needed, handle here.
        // For now, DB cascade is assumed on the migration for order_item_incomes.
    }
}