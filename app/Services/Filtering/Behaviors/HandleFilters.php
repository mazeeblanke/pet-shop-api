<?php

namespace App\Services\Filtering\Behaviors;

use App\Services\Filtering\Contracts\Filter;
use Illuminate\Contracts\Database\Eloquent\Builder;

trait HandleFilters
{
    /**
     * Apply all filters.
     *
     * @param  Builder   $query
     * @param  Filter $filters
     *
     * @return Builder
     */
    public function scopeFilter(Builder $query, Filter $filters): Builder
    {
        return $filters->apply($query, $this);
    }
}
