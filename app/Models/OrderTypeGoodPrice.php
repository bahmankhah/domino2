<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTypeGoodPrice extends Model {

    protected $fillable = ['order_type_id', 'good_id', 'price'];
     protected $table = 'order_type_good_prices'; 
     
     public function orderType() {
         return $this->belongsTo(OrderType::class);
     }

     public function good() {
         return $this->belongsTo(Good::class);
     }


}