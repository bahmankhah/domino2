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
}