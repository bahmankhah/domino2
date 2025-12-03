<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'delivered_by_id',
        'delivered_at',
        'fee',
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
    ];

    /**
     * Relationship: Delivery belongs to an order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relationship: Delivery was done by a user.
     */
    public function deliveredBy()
    {
        return $this->belongsTo(User::class, 'delivered_by_id');
    }

    /**
     * Accessor: Get order items through the order relationship
     */
    public function getOrderItemsAttribute()
    {
        return $this->order?->items ?? collect();
    }

    /**
     * Check if this delivery is marked as delivered
     */
    public function isDelivered(): bool
    {
        return $this->delivered_at !== null;
    }

    /**
     * Get status based on delivered_at
     */
    public function getStatusAttribute(): string
    {
        return $this->delivered_at ? 'delivered' : 'pending';
    }
}
