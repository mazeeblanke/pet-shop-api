<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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

        if (is_array($this->resource) && isset($this->resource['token'])) {
            return $this->resource;
        }

        return [
            'uuid' => $this->resource->uuid,
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'email' => $this->resource->email,
            'avatar' => $this->resource->avatar,
            'address' => $this->resource->address,
            'phone_number' => $this->resource->phone_number,
            'is_marketing' => $this->resource->is_marketing,
            'updated_at' => $this->resource->updated_at,
            'created_at' => $this->resource->created_at,
            'email_verified_at' => $this->resource->email_verified_at,
            'last_login_at' => $this->resource->last_login_at,
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
