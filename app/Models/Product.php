<?php

namespace App\Models;

use App\Services\Filtering\Behaviors\HandleFilters;
use App\Services\Filtering\Contracts\Filter;
use App\Services\Filtering\Contracts\Filterable;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Product
 *
 * @property int $id
 * @property string $uuid
 * @property string $title
 * @property float $price
 * @property string $description
 * @property string $category_uuid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array|null $metadata
 * @property string|null $deleted_at
 *
 * @property-read \App\Models\Brand|null $brand
 * @property-read \App\Models\Category|null $category
 * @property-read mixed $brand_uuid
 *
 * @method static ProductFactory factory($count = null, $state = [])
 * @method static Builder|Product filter(Filter $filters)
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product query()
 * @method static Builder|Product whereCategoryUuid($value)
 * @method static Builder|Product whereCreatedAt($value)
 * @method static Builder|Product whereDeletedAt($value)
 * @method static Builder|Product whereDescription($value)
 * @method static Builder|Product whereId($value)
 * @method static Builder|Product whereMetadata($value)
 * @method static Builder|Product wherePrice($value)
 * @method static Builder|Product whereTitle($value)
 * @method static Builder|Product whereUpdatedAt($value)
 * @method static Builder|Product whereUuid($value)
 *
 * @mixin \Eloquent
 */
class Product extends Model implements Filterable
{
    use HasFactory;
    use HandleFilters;

    protected $casts = [
        'metadata' => 'array',
    ];

    protected $fillable = [
        'uuid',
        'category_uuid',
        'title',
        'description',
        'price',
        'metadata',
    ];

    public function getBrandUuidAttribute(): ?string
    {
        $metadata = $this->metadata ?? [];

        if (! is_array($metadata)) {
            $metadata = json_decode($metadata, true);
        }

        return $metadata['brand'];
    }

    /**
     * Brand Relationship
     */
    public function brand(): HasOne
    {
        return $this->hasOne(Brand::class, 'uuid', 'brand_uuid');
    }

    /**
     * Category Relationship
     */
    public function category(): HasOne
    {
        return $this->hasOne(Category::class, 'uuid', 'category_uuid');
    }
}
