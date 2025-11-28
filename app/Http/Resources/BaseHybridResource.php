<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BaseHybridResource extends JsonResource
{
    /**
     * Helper to get a value whether the resource is a Model or an Array.
     */
    protected function getVal(string $key, mixed $default = null): mixed
    {
        if (is_array($this->resource)) {
            return $this->resource[$key] ?? $default;
        }
        return $this->resource->{$key} ?? $default;
    }
}