<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class BrandCollection extends Collection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => BrandResource::collection($this->collection),
        ];
    }
}
