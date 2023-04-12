<?php

namespace App\Repositories;

use App\Services\Filtering\Contracts\Filter;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Services\Filtering\Behaviors\HandleFilters;

abstract class Repository
{
    protected Model $model;

    protected string $modelIdKey = 'id';

    protected array $reservedFields = [];

    /**
     * Defer unknown calls to the base model
     */
    public function __call(string $method, array $parameters): mixed
    {
        return $this->model->$method(...$parameters);
    }

    public function get(
        array $filters = [],
        array $with = []
    ): LengthAwarePaginator | Collection {
        $limit = $filters['limit'];
        $page = $filters['page'];

        $builder = $this->model->with($with);

        if ($this->supportsFiltering()) {
            $builder = $builder->filter($this->getFilter());
        }

        return $builder->paginate($limit, ['*'], 'page', $page);
    }

    /**
     *
     */
    public function getFilter(): Filter
    {
        $namepace = trim(app()->getNamespace(), '\\') . "\Services\Filtering\\";
        $filter = $namepace . class_basename($this->model).'Filter';

        if (class_exists($filter)) {
            return app()->make($filter);
        }

        throw new Exception('Filter class ' . $filter . ' Does not exist');
    }

    public function supportsFiltering(): bool
    {
        return in_array(
            HandleFilters::class,
            class_uses($this->model)
        );
    }
}
