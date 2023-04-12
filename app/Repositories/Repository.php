<?php

namespace App\Repositories;

use App\Services\Filtering\Contracts\Filter;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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

        return $this->model->with($with)
            ->filter($this->getFilter())
            ->paginate($limit, ['*'], 'page', $page);
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
}
