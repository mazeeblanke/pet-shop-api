<?php

namespace App\Services\Filtering\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @method static filter(Filter $filters)
 * @method filter(Filter $filters)
 */
interface Filterable
{
    public function scopeFilter(Builder $query, Filter $filters): Builder;
}
