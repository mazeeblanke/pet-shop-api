<?php

namespace App\Services\Filtering\Behaviors;

use App\Services\Filtering\Contracts\Filter;
use Illuminate\Contracts\Database\Eloquent\Builder;

trait HandleFilters
{
    /**
     * Apply all filters.
     */
    public function scopeFilter(Builder $query, Filter $filters): Builder
    {
        return $filters->apply($query, $this);
    }
}
