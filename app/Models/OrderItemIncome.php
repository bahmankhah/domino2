<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemIncome extends Model
{
    protected $fillable = ['price_rule_id', 'order_item_id', 'credit', 'debit', 'received_by', 'received_at'];
    
    protected $casts = ['received_at' => 'datetime'];

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function priceRule(): BelongsTo
    {
        return $this->belongsTo(IncomePriceRule::class, 'price_rule_id');
    }
}