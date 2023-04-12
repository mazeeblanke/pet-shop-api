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
}
