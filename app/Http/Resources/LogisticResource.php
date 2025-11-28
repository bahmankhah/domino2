<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class LogisticResource extends BaseHybridResource
{
    public function toArray(Request $request): array
    {
        if (!$this->resource) return [];

        return [
            'id' => $this->getVal('id'),
            'name' => $this->getVal('name'),
        ];
    }
}
