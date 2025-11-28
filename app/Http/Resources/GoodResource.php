<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;

class GoodResource extends BaseHybridResource
{
    public function toArray(Request $request): array
    {
        if (!$this->resource) return [];

        return [
            'id' => $this->getVal('id'),
            'title' => $this->getVal('title'),
            'code' => $this->getVal('code'),
            'image' => $this->getVal('image'),
            'description' => $this->getVal('description'),
        ];
    }
}