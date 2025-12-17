<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderType extends Model
{
    protected $fillable = ['name', 'description', 'duration_days'];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function goodPrices()
    {
        return $this->hasMany(OrderTypeGoodPrice::class);
    }

    public function goods()
    {
        return $this->belongsToMany(Good::class, 'order_type_good_prices', 'order_type_id', 'good_id')
                    ->withPivot('price')
                    ->withTimestamps();
    }
}