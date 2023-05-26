<?php

namespace App\Http\Controllers\Concerns;

use App\Repositories\Repository;
use App\Services\Filtering\Contracts\Filterable;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/**
 * @method getRequestClass(string $type)
 * @method getCollectionClass()
 * @method getRepositoryClass()
 */
trait Controller
{
    /**
     * @param  string  $method
     * @param  array  $args
     */
    public function __call($method, $args): string
    {
        if (preg_match('/^get(\w+)Class$/', $method, $matches)) {
            return $this->getClass($matches[1], $args);
        }

        return parent::__call($method, $args);
    }

    /**
     * Retrieve form class
     */
    public function getFormRequestClass(string $type = 'store'): string
    {
        return $this->getRequestClass($type);
    }

    /**
     * Resolve an instance of the resource class from the container
     *
     * @return  JsonResource[return description]
     */
    protected function makeResource(
        Filterable|Model|LengthAwarePaginator|array|null $data = null,
        bool $collection = false
    ): JsonResource {
        $resource = $collection
            ? $this->collection
            : $this->resource;

        return $this->app->make($resource, [
            'resource' => $data ?? [],
        ]);
    }

    /**
     * Get class
     *
     * @throws Exception
     */
    protected function getClass(string $type, array $args = []): string
    {
        $prefix = '';

        if (count($args) > 0) {
            $prefix = ucfirst($args[0]);
        }

        $modelName = $prefix . $this->modelName;
        $class = $this->getBaseDir($type) . $modelName . $type;

        if (class_exists($class)) {
            return $class;
        }

        throw new Exception("{$type} class {$class} not found");
    }

    protected function getBaseDir(string $type): string
    {
        return match ($type) {
            'Repository' => "{$this->namespace}\Repositories\\",
            'Resource' => "{$this->namespace}\Http\Resources\\",
            'Collection' => "{$this->namespace}\Http\Resources\\",
            'Request' => "{$this->namespace}\Http\Requests\\",
            default => ''
        };
    }

    /**
     * Get Model name
     */
    protected function getModelName(): string
    {
        return preg_replace('/Controller$/', '', class_basename($this)) ?? $this->modelName;
    }

    /**
     * Get Respository class
     */
    protected function getRepository(): Repository
    {
        return $this->app->make($this->getRepositoryClass());
    }

    /**
     * Get Namespace
     */
    protected function getNamespace(): string
    {
        return $this->namespace ?? trim($this->app->getNamespace(), '\\');
    }

    /**
     * validate request
     */
    protected function validateFormStoreRequest(): Request
    {
        return $this->app->make($this->getFormRequestClass());
    }

    /**
     * extra data during create
     */
    protected function getExtraData(Request $request): array
    {
        return [
            'slug' => Str::slug($request->title),
        ];
    }
}
