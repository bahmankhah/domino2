<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Warehouse extends Model
{
    protected $fillable = ['title', 'description'];

    public function providers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'warehouse_providers', 'warehouse_id', 'provider_id')
                    ->withPivot('ownership_percent')
                    ->withTimestamps();
    }
}