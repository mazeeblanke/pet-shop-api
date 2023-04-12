<?php

namespace App\Services\Filtering;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProductFilter extends BaseFilter
{
    /**
     * Specific filters.
     *
     */
    protected array $filters = [
        'title',
        'price',
        'category',
        'brand',
    ];

    /**
     * Filter title.
     *
     */
    protected function title(): Builder
    {
        return $this
            ->builder
            ->whereTitle(
                $this->request->title
            );
    }

    /**
     * Filter price.
     *
     */
    protected function price($value): Builder
    {
        return $this->builder->wherePrice($value);
    }

    /**
     * Filter category.
     *
    */
    protected function category($value): Builder
    {
        return $this
            ->builder
            ->whereHas(
                'category',
                fn ($query) => $query->where('uuid', $value)
            );
    }

    /**
     * Filter brand.
     *
    */
    protected function brand($value): Builder
    {
        $joinOnColumn = DB::raw(
            "JSON_UNQUOTE(JSON_EXTRACT(products.metadata, '$.brand'))"
        );
        return $this->builder
            ->join(
                'brands',
                'brands.uuid',
                $joinOnColumn
            )
            ->where('brands.uuid', $value);
    }
}
