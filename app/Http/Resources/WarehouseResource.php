<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
class WarehouseResource extends BaseHybridResource
{
    public function toArray(Request $request): array
    {
        if (!$this->resource) return [];

        return [
            'id' => $this->getVal('id'),
            'title' => $this->getVal('title'),
        ];
    }
}