<?php

namespace App\Models;

use App\Services\Filtering\Behaviors\HandleFilters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory, HandleFilters;

    protected $casts = [
        'metadata' => 'array',
    ];

    public function getBrandUuidAttribute()
    {
        $metadata = $this->metadata;

        if (! is_array($metadata)) {
            $metadata = json_decode($metadata, true);
        }

        return $metadata['brand'];
    }

    /**
     * Brand Relationship
     *
     */
    public function brand(): HasOne
    {
        return $this->hasOne(Brand::class, 'uuid', 'brand_uuid');
    }

    /**
     * Category Relationship
     *
     */
    public function category(): HasOne
    {
        return $this->hasOne(Category::class, 'uuid', 'category_uuid');
    }
}
