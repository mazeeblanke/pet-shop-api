<?php

namespace App\Http\Resources;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ErrorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $errors = [];
        $error = $this->resource->getMessage();

        if (isset($this->resource->validator)) {
            $errors = $this->resource->validator->errors();
            $error = 'Failed Validation';
        }

        if ($this->resource instanceof Exception) {
            $prev = $this->resource->getPrevious();
            if ($prev) {
                $errors[] = $prev->getMessage();
            }
        }

        return [
            'success' => 0,
            'data' => [],
            'error' => $error,
            'errors' => $errors,
            'trace' => [],
            // 'trace' => $this->getTrace(),
        ];
    }
}
