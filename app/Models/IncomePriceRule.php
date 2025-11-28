<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomePriceRule extends Model
{
    protected $fillable = ['type', 'percentage', 'fallback_type'];
}