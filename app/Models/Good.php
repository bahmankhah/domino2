<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Good extends Model
{
    protected $fillable = ['code', 'title', 'description', 'category_id', 'warehouse_id', 'image', 'is_available'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function providers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'good_providers', 'good_id', 'provider_id')
                    ->withPivot('ownership_percent')
                    ->withTimestamps();
    }

    public function prices(): BelongsToMany
    {
        return $this->belongsToMany(OrderType::class, 'order_type_good_prices', 'good_id', 'order_type_id')
                    ->withPivot('price', 'supplier_price')
                    ->withTimestamps();
    }
}