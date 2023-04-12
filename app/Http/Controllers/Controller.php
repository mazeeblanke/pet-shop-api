<?php

namespace App\Http\Controllers;

use App\Repositories\Repository;
use Exception;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Pet Shop API - Swagger Documentation",
 *      description="This API has been created with the goal to test the coding skills of the candidates who are applying for a job position at Buckhill",
 * )
 *
 * @OA\SecurityScheme(
 *   securityScheme="bearerAuth",
 *   in="header",
 *   name="Authorization",
 *   scheme="bearer",
 *   type="http",
 *   description=""
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected Application $app;

    protected string $modelName;

    protected string $namespace;

    protected string $resource;

    protected string $collection;

    protected Repository $repository;

    protected array $with = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->namespace = $this->getNamespace();
        $this->modelName = $this->getModelName();
        $this->repository = $this->getRepository();
        $this->resource = $this->getResourceClass();
        $this->collection = $this->getCollectionClass();
    }

    /**
     * Display resource listing.
     */
    public function index(Request $request)
    {
        $filters = [
            'limit' => $request->get('limit', 10),
            'page' => $request->get('page', 1),
        ];

        $data = $this->repository->get($filters, $this->with);

        return $this->respondWithSuccess(new $this->collection($data));
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $model = $this->repository->getById($uuid, $this->with);

        if (! $model) {
            return $this->respondWithError($this->getModelName() . ' not found');
        }

        return $this->respondWithSuccess(new $this->resource($model));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if ($this->repository->delete($id)) {
            return $this->respondWithSuccess(new $this->resource(null));
        }

        return $this->respondWithError($this->getModelName() .' not found');
    }

    /**
     * @return  string
     */
    protected function getRepositoryClass($model): string
    {
        $class = "{$this->namespace}\Repositories\\" . $model . 'Repository';
        if (class_exists($class)) {
            return $class;
        }

        throw new Exception('Repository class ' . $class .  ' not found');
    }

    /**
     *
     */
    protected function respondWithSuccess(JsonResource $resource): JsonResponse
    {
        return $resource->response()->setStatusCode(Response::HTTP_OK);
    }

    /**
     *
     */
    protected function respondWithError(string $message, $status = Response::HTTP_NOT_FOUND, $previousException = null)
    {
        throw new Exception($message, $status, $previousException);
    }

    /**
     * Get Model name
     */
    protected function getModelName(): string
    {
        return preg_replace('/Controller$/', '', class_basename($this)) ?? $this->modelName;
    }

    /**
     * Get Resource class
     */
    protected function getResourceClass(): string
    {
        $class = "{$this->namespace}\Http\Resources\\" . $this->modelName . 'Resource';
        if (class_exists($class)) {
            return $class;
        }

        throw new Exception('Resource class ' . $class . ' not found');
    }

    /**
     * Get Collection class
     */
    protected function getCollectionClass(): string
    {
        $class = "{$this->namespace}\Http\Resources\\" . $this->modelName . 'Collection';
        if (class_exists($class)) {
            return $class;
        }

        throw new Exception('Collection class ' . $class . ' not found');
    }

    /**
     * Get Respository class
     */
    protected function getRepository(): Repository
    {
        return $this->app->make($this->getRepositoryClass($this->modelName));
    }

    /**
     * Get Namespace
     */
    protected function getNamespace(): string
    {
        return $this->namespace ?? trim($this->app->getNamespace(), '\\');
    }
}
