<?php

namespace App\Repositories;

use App\Services\Filtering\Behaviors\HandleFilters;
use App\Services\Filtering\Contracts\Filter;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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

    /**
     * Fetch all
     */
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
     * Fetch by id
     */
    public function getById($uuid, array $with = []): Model|null
    {
        return $this->model
            ->with($with)
            ->where($this->modelIdKey, $uuid)
            ->first();
    }

    /**
     *  store a resource
     */
    public function create(array $fields): Model
    {
        return DB::transaction(function () use ($fields) {
            $object = $this->model
                ->create(Arr::except(
                    $fields,
                    $this->getReservedFields()
                ));

            $object->save();

            $this->afterCreate($object);

            return $object;
        }, 3);
    }

    /**
     * Update fields
     */
    public function update(mixed $id, array $fields): Model|bool
    {
        return DB::transaction(function () use ($id, $fields) {
            $object = $this->model->where($this->modelIdKey, $id)->first();

            if (! $object) {
                return false;
            }

            $object->fill(Arr::except($fields, $this->getReservedFields()));

            $object->save();

            return $object->fresh();
        }, 3);
    }

    /**
     * Delete a resource
     */
    public function delete($id): bool
    {
        return DB::transaction(function () use ($id) {
            $object = $this->model->where($this->modelIdKey, $id)->first();
            if ($object === null) {
                return false;
            }

            if ($object->delete()) {
                return true;
            }
            return false;
        }, 3);
    }

    /**
     *  Get reserved fields
     */
    protected function getReservedFields(): array
    {
        return $this->reservedFields;
    }

    /**
     * Hook after create
     *
     */
    public function afterCreate($object): void
    {
        $object->save();
    }

    /**
     *  Get filter instance
     */
    private function getFilter(): Filter
    {
        $namepace = trim(app()->getNamespace(), '\\') . "\Services\Filtering\\";
        $filter = $namepace . class_basename($this->model).'Filter';

        if (class_exists($filter)) {
            return app()->make($filter);
        }

        throw new Exception('Filter class ' . $filter . ' Does not exist');
    }

    /**
     * Check for filtering support by model
     */
    private function supportsFiltering(): bool
    {
        return in_array(
            HandleFilters::class,
            class_uses($this->model)
        );
    }
}
