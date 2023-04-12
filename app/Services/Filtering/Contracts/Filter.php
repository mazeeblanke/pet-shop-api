<?php

namespace App\Services\Filtering\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface Filter
{
    public function apply(Builder $builder, Model $model): Builder;
}
