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
            return [ 'uuid' => $this->resource->uuid ];
        }

        return [
            'uuid' => $this->resource->uuid,
            'category_uuid' => $this->resource->category_uuid,
            'title' => $this->resource->title,
            'price' => $this->resource->price,
            'description' => $this->resource->description,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
            'deleted_at' => $this->resource->deleted_at,
            'metadata' => $this->resource->metadata,
            'category' => new CategoryResource($this->resource->category),
            'brand' => new BrandResource($this->resource->brand),
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
