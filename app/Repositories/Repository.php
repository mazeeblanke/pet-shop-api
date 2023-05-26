<?php

namespace App\Repositories;

use App\Services\Filtering\Contracts\Filterable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * @method getFilter()
 */
abstract class Repository
{
    protected Filterable|Model $model;

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
    public function get(array $filters = [], array $with = []): LengthAwarePaginator
    {
        $limit = $filters['limit'];
        $page = $filters['page'];
        $builder = $this->model;

        if ($builder instanceof Filterable) {
            $builder = $builder->filter($this->getFilter());
        }

        $builder = $builder->with($with);

        return $builder->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * Fetch by id
     */
    public function getById(string|int $id, array $with = []): Model|Filterable|null
    {
        return $this->model
            ->with($with)
            ->where($this->modelIdKey, $id)
            ->first();
    }

    /**
     *  store a resource
     */
    public function create(array $fields): Model|Filterable
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
    public function update(string|int $id, array $fields): Model|filterable|bool|null
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
    public function delete(string|int $id): bool
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
     * Hook after create
     */
    public function afterCreate(Model $object): void
    {
        $object->save();
    }

    /**
     *  Get reserved fields
     */
    protected function getReservedFields(): array
    {
        return $this->reservedFields;
    }
}
