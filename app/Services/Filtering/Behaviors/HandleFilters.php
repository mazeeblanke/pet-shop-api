<?php

namespace App\Services\Filtering\Behaviors;

use App\Services\Filtering\Contracts\Filter;
use Exception;
use Illuminate\Database\Eloquent\Builder;

trait HandleFilters
{
    /**
     * Apply all filters.
     */
    public function scopeFilter(Builder $query, Filter $filters): Builder
    {
        return $filters->apply($query, $this);
    }

    /**
     *  Get filter instance
     */
    public function getFilter(): Filter
    {
        $namepace = trim(app()->getNamespace(), '\\') . "\Services\Filtering\\";
        $filter = $namepace . class_basename($this).'Filter';

        if (class_exists($filter)) {
            return app()->make($filter);
        }

        throw new Exception('Filter class ' . $filter . ' Does not exist');
    }
}
