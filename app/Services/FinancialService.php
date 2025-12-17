<?php

namespace App\Services;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\IncomePriceRule;
use App\Models\OrderItemIncome;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class FinancialService
{
    /**
     * PHASE 1: Calculation
     * Deletes old estimated incomes for this item and recalculates based on current rules/providers.
     * Should be called on OrderItem create/update.
     */
    public function calculateItemIncomes(OrderItem $item): void
    {
        // Don't modify incomes if order is already completed/locked
        if ($item->order && $item->order->status === 'completed') {
            return;
        }

        DB::transaction(function () use ($item) {
            // 1. Wipe existing AUTOMATIC draft incomes for this item.
            // We preserve incomes where the rule has NO percentage (Manual entries).
            $autoRuleIds = IncomePriceRule::whereNotNull('percentage')->pluck('id');
            $item->incomes()->whereIn('price_rule_id', $autoRuleIds)->delete();

            if (!$item->price || $item->price <= 0) return;

            // 2. Load Rules
            $rules = IncomePriceRule::whereNotNull('percentage')->get();
            
            // 3. Build Effective Percentages Map
            $percentageMap = [];
            $ruleObjects = [];

            foreach ($rules as $rule) {
                $percentageMap[$rule->type] = $rule->percentage;
                $ruleObjects[$rule->type] = $rule;
            }

            // 4. Resolve Fallbacks
            $finalMap = $percentageMap;
            foreach ($percentageMap as $type => $percent) {
                if (!$this->hasProviderForType($item, $type)) {
                    $fallback = $ruleObjects[$type]->fallback_type ?? null;
                    if ($fallback && isset($finalMap[$fallback])) {
                        $finalMap[$fallback] += $percent;
                        $finalMap[$type] = 0;
                    }
                }
            }

            // 5. Create Income Records (Drafts)
            $totalPrice = $item->price;
            foreach ($finalMap as $type => $effectivePercent) {
                if ($effectivePercent <= 0) continue;

                $amountForThisType = ($totalPrice * $effectivePercent) / 100;
                $originalRule = $ruleObjects[$type];

                switch ($type) {
                    case 'warehouse_provider':
                        $this->createIncomeRecords($item->warehouse, $amountForThisType, $originalRule, $item);
                        break;
                    case 'good_provider':
                        $amountForThisType = $item->good_supplier_price ? $item->good_supplier_price : $amountForThisType;
                        $this->createIncomeRecords($item->good, $amountForThisType, $originalRule, $item);
                        break;
                    case 'logistics_provider':
                        $this->createIncomeRecords($item->logistic, $amountForThisType, $originalRule, $item);
                        break;
                    case 'referrer_provider':
                        if ($item->referrer_id) {
                            $this->recordSingleIncome($item->referrer, $amountForThisType, $originalRule, $item);
                        }
                        break;
                }
            }
        });
    }

    /**
     * PHASE 2: Commitment
     * Takes the existing OrderItemIncome records and turns them into actual Transactions.
     * Updates User wallets.
     */
    public function commitOrderTransactions(Order $order): void
    {
        DB::transaction(function () use ($order) {
            // Get all incomes for this order that haven't been processed yet
            $incomes = $order->incomes()->whereNull('received_at')->with(['receivedBy', 'orderItem.good'])->get();

            foreach ($incomes as $income) {
                if (!$income->receivedBy) continue;

                // Create Transaction
                $currentBalance = $income->receivedBy->wallet ?? 0;
                $newBalance = $currentBalance + $income->credit;

                $goodTitle = $income->orderItem?->good?->title ?? 'Unknown Item';
                Transaction::create([
                    'user_id' => $income->receivedBy->id,
                    'order_id' => $order->id,
                    'type' => 'income',
                    'description' => "Income from Order #{$order->id} (Item: {$goodTitle})",
                    'credit' => $income->credit,
                    'debit' => 0,
                    'remain' => $newBalance,
                ]);

                $income->update([
                    'received_at' => now(),
                ]);

                // Update Wallet
                $income->receivedBy->update(['wallet' => $newBalance]);
            }

            $deliveries = $order->deliveries;
            foreach($deliveries as $delivery) {
                if (!$delivery->delivered_at) continue;

                // Create Transaction for delivery fee if applicable
                if ($delivery->fee && $delivery->fee > 0) {
                    $deliverer = $delivery->deliveredBy;
                    if ($deliverer) {
                        // Check if delivery fee transaction already exists for this delivery
                        $existingTransaction = Transaction::where('order_id', $order->id)
                            ->where('user_id', $deliverer->id)
                            ->where('type', 'income')
                            ->where('description', 'like', "Delivery Fee from Order #{$order->id}%")
                            ->first();

                        if (!$existingTransaction) {
                            $currentBalance = $deliverer->wallet ?? 0;
                            $newBalance = $currentBalance + $delivery->fee;

                            Transaction::create([
                                'user_id' => $deliverer->id,
                                'order_id' => $order->id,
                                'type' => 'income',
                                'description' => "Delivery Fee from Order #{$order->id}",
                                'credit' => $delivery->fee,
                                'debit' => 0,
                                'remain' => $newBalance,
                            ]);

                            // Update Wallet
                            $deliverer->update(['wallet' => $newBalance]);
                        }
                    }
                }
            }

            // Create customer debit transaction for the total order amount
            $this->createCustomerDebitTransaction($order);
            
            // Don't update status here - it's already 'completed' when this method is called
        });
    }

     /**
     * Handle User Settlements (Payouts)
     */
    public function processSettlement(User $user, int $amount, ?string $description = null): void
    {
        if ($amount <= 0) return;

        DB::transaction(function () use ($user, $amount, $description) {
            // Refresh user to get latest wallet balance
            $user->refresh();

            if ($user->wallet < $amount) {
                // Optional: throw exception or handle error
                // For now, we assume validation happened in UI, but safe to check
                throw new \Exception("Insufficient wallet balance.");
            }

            $currentBalance = $user->wallet;
            $newBalance = $currentBalance - $amount;

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'settlement',
                'description' => $description ?? 'Settlement / Payout',
                'credit' => 0,
                'debit' => $amount,
                'remain' => $newBalance,
            ]);

            $user->update(['wallet' => $newBalance]);
        });
    }
    
    /**
     * No automatic customer transactions - payments handled manually
     * Customers pay via wire transfer (order items) or cash (delivery fees)
     */
    private function createCustomerDebitTransaction(Order $order): void
    {
        // No automatic customer transactions
        // Payments are handled outside the system:
        // - Order items: Wire transfer (recorded manually by admin)
        // - Delivery fees: Cash collected by driver (tracked in delivery records)
        return;
    }

    // --- Helpers ---

    private function hasProviderForType(OrderItem $item, string $type): bool
    {
        return match ($type) {
            'warehouse_provider' => count($item->warehouse?->providers ?? []) != 0,
            'good_provider' => count($item->good?->providers ?? []) != 0,
            'logistics_provider' => count($item->logistic?->providers ?? []) != 0,
            'referrer_provider' => !is_null($item->referrer_id),
            'delivery' => true,
            default => false,
        };
    }

    private function createIncomeRecords($model, $totalAmount, $rule, $item)
    {
        if (!$model) return;

        foreach ($model->providers as $provider) {
            $providerSharePercent = $provider->pivot->ownership_percent; 
            $providerAmount = ($totalAmount * $providerSharePercent) / 100;

            if ($providerAmount <= 0) continue;
            
            $this->recordSingleIncome($provider, $providerAmount, $rule, $item);
        }
    }

    private function recordSingleIncome($user, $amount, $rule, $item)
    {
        if(!$user) return;
        OrderItemIncome::create([
            'price_rule_id' => $rule->id,
            'order_item_id' => $item->id,
            'credit' => $amount,
            'debit' => 0,
            'received_by' => $user->id,
            'received_at' => null, // Date calculated
        ]);
    }
}