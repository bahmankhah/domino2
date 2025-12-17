<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Transaction;
use App\Services\FinancialService;

class OrderObserver
{
    public function updated(Order $order): void
    {
        // Check if status was changed to completed
        if ($order->isDirty('status') && $order->status === 'completed') {
            $this->handleOrderCompletion($order);
        }
        
        // Check if status was changed to canceled
        if ($order->isDirty('status') && $order->status === 'canceled') {
            $this->handleOrderCancellation($order);
        }
        
        // Check if status was changed to completed or canceled
        if ($order->isDirty('status') && in_array($order->status, ['completed', 'canceled'])) {
            $this->makeGoodsAvailable($order);
        }
    }

    public function creating(Order $order): void
    {
        // Auto-create customer if no customer_id is provided
        if (!$order->customer_id && $order->customer_name && $order->customer_mobile) {
            $customer = $this->createCustomerFromOrderData($order);
            $order->customer_id = $customer->id;
        }
    }

    public function updating(Order $order): void
    {
        // Auto-create customer if no customer_id is provided during update
        if (!$order->customer_id && $order->customer_name && $order->customer_mobile) {
            $customer = $this->createCustomerFromOrderData($order);
            $order->customer_id = $customer->id;
        }

        // Check if status was changed to completed
        if ($order->isDirty('status') && $order->status === 'completed') {
            $this->handleOrderCompletion($order);
        }
        
        // Check if status was changed to canceled
        if ($order->isDirty('status') && $order->status === 'canceled') {
            $this->handleOrderCancellation($order);
        }
        
        // Check if status was changed to completed or canceled
        if ($order->isDirty('status') && in_array($order->status, ['completed', 'canceled'])) {
            $this->makeGoodsAvailable($order);
        }
    }

    /**
     * Create a customer from order data
     */
    protected function createCustomerFromOrderData(Order $order): \App\Models\User
    {
        // Generate a unique email based on mobile number
        $email = 'customer_' . $order->customer_mobile . '@rental.local';
        
        // Check if user with this mobile already exists
        $existingUser = \App\Models\User::where('mobile', $order->customer_mobile)->first();
        if ($existingUser) {
            return $existingUser;
        }

        return \App\Models\User::create([
            'name' => $order->customer_name,
            'email' => $email,
            'mobile' => $order->customer_mobile,
            'address' => $order->customer_address,
            'password' => bcrypt('password'), // Default password
            'role' => 'customer',
            'wallet' => 0,
        ]);
    }

    /**
     * Handle order completion by committing financial transactions
     */
    protected function handleOrderCompletion(Order $order): void
    {
        $financialService = app(FinancialService::class);
        $financialService->commitOrderTransactions($order);
    }

    /**
     * Handle order cancellation by deleting transactions and reversing wallet changes
     */
    protected function handleOrderCancellation(Order $order): void
    {
        // Get all transactions for this order
        $transactions = Transaction::where('order_id', $order->id)->get();
        
        foreach ($transactions as $transaction) {
            // Reverse the wallet changes before deleting the transaction
            if ($transaction->user) {
                $currentBalance = $transaction->user->wallet ?? 0;
                $reversedBalance = $currentBalance - $transaction->credit + $transaction->debit;
                $transaction->user->update(['wallet' => $reversedBalance]);
            }
        }
        
        // Delete all transactions for this order
        Transaction::where('order_id', $order->id)->delete();
        
        // Reset income received_at timestamps to null (mark as not received)
        $order->incomes()->update(['received_at' => null]);
    }

    /**
     * Make all goods in the order available again
     */
    protected function makeGoodsAvailable(Order $order): void
    {
        // Get all goods from order items and make them available
        $order->items()->with('good')->each(function ($orderItem) {
            if ($orderItem->good) {
                $orderItem->good->update(['is_available' => true]);
            }
        });
    }
}