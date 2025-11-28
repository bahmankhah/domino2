<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Logistic extends Model
{
    protected $fillable = ['name', 'description', 'image'];

    public function providers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'logistic_providers', 'logistic_id', 'provider_id')
                    ->withPivot('ownership_percent')
                    ->withTimestamps();
    }
}
