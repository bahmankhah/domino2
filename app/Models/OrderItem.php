<?php

namespace App\Models;

use App\Http\Resources\GoodResource;
use App\Http\Resources\WarehouseResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    protected $fillable = [
        'warehouse_id', 'warehouse_info',
        'logistic_id', 'logistic_info',
        'order_id',
        'good_id', 'good_info',
        'referrer_id', 'referrer_info',
        'damage', 'price',
        'order_type_id', 'order_type_info',
        'started_at', 'ended_at'
    ];

    protected $casts = [
        'warehouse_info' => 'array',
        'logistic_info' => 'array',
        'good_info' => 'array',
        'referrer_info' => 'array',
        'order_type_info' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function good(): BelongsTo { return $this->belongsTo(Good::class); }
    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class); }
    public function logistic(): BelongsTo { return $this->belongsTo(Logistic::class); }
    public function orderType(): BelongsTo { return $this->belongsTo(OrderType::class); }
    public function incomes(): HasMany { return $this->hasMany(OrderItemIncome::class); }

    // Helpers to get the "Effective" data (Live or Snapshot)
    public function getEffectiveGoodAttribute()
    {
        // If relation exists, use it. If not, use snapshot.
        return $this->good ? new GoodResource($this->good) : new GoodResource($this->good_info);
    }

    public function getEffectiveWarehouseAttribute()
    {
        return $this->warehouse ? new WarehouseResource($this->warehouse) : new WarehouseResource($this->warehouse_info);
    }
}