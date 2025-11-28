<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;

class OrderTypeResource extends BaseHybridResource
{
    public function toArray(Request $request): array
    {
        if (!$this->resource) return [];

        return [
            'id' => $this->getVal('id'),
            'name' => $this->getVal('name'),
            'duration_days' => $this->getVal('duration_days'),
        ];
    }
}