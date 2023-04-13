<?php

namespace App\Services\Filtering;

use App\Services\Filtering\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

abstract class BaseFilter implements Filter
{
    /**
     * The Eloquent Query builder.
     */
    protected Builder $builder;

    protected Model $model;

    protected Request $request;

    /**
     * Default filters
     */
    protected array $defaultFilters = [
        'orderByColumn' => [],
    ];

    /**
     * Specific filters.
     */
    protected array $filters = [];

    /**
     * Create a new Filters instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply the filters.
     */
    public function apply(Builder $builder, Model $model): Builder
    {
        $this->builder = $builder;
        $this->model = $model;

        foreach ($this->getFilters() as $filter => $value) {
            if (method_exists($this, $filter)) {
                $this->$filter($value);
            }
        }

        return $this->builder;
    }

    /**
     * Fetch all filters (Specific + Default).
     *
     * @return array
     */
    public function getFilters(): array
    {
        $filters = $this->request->only($this->filters);
        return array_filter($filters) + $this->defaultFilters;
    }

    /**
     * Filter the query by ordering.
     *
     * @return Builder
     */
    protected function orderByColumn(): Builder
    {
        $direction = (bool) $this->request->get('desc', false) === true
            ? 'desc'
            : 'asc';
        $sortBy = $this->request->get('sortBy', null);

        if (Schema::hasColumn($this->model->getTable(), $sortBy)) {
            return $this->builder->orderBy($sortBy, $direction);
        }

        return $this->builder;
    }
}
