<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (! $this->resource) {
            return [];
        }

        if ($this->resource->wasRecentlyCreated) {
            return [ 'uuid' => $this->uuid ];
        }

        return [
            'uuid' => $this->uuid,
            'category_uuid' => $this->category_uuid,
            'title' => $this->title,
            'price' => $this->price,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'metadata' => $this->metadata,
            'category' => new CategoryResource($this->category),
            'brand' => new BrandResource($this->brand),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'error' => null,
            'errors' => [],
            'extra' => [],
            'success' => 1,
        ];
    }
}
