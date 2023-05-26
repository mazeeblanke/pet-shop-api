<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Controller as ControllerConcern;
use App\Repositories\Repository;
use Exception;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;
use Throwable;

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
 *
 * @method getResourceClass()
 * @method getCollectionClass()
 */
class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;
    use ControllerConcern;

    protected Application $app;

    protected string $modelName;

    protected string $namespace;

    protected string $resource;

    protected string $collection;

    protected Repository $repository;

    protected CacheRepository $cache;

    protected array $with = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->namespace = $this->getNamespace();
        $this->modelName = $this->getModelName();
        $this->repository = $this->getRepository();
        $this->resource = $this->getResourceClass();
        $this->collection = $this->getCollectionClass();
        $this->cache = $this->app->make(CacheRepository::class);
    }

    /**
     * Display resource listing.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'limit' => $request->get('limit', 10),
            'page' => $request->get('page', 1),
        ];

        $data = $this->repository->get($filters, $this->with);

        $collection = $this->makeResource($data, true);

        return $this->respondWithSuccess($collection);
    }

    /**
     * store a resource.
     */
    public function store(): JsonResponse
    {
        $request = $this->validateFormStoreRequest();

        $data = array_merge(
            $request->all(),
            ['uuid' => Str::uuid()],
            $this->getExtraData($request)
        );

        try {
            $model = $this->repository->create($data);
        } catch (Exception $e) {
            $this->respondWithError(
                'Failed to create new '. $this->getModelName(),
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $e
            );
        }

        $resource = $this->makeResource($model);

        return $this->respondWithSuccess($resource);
    }

    /**
     * Display the specified resource.
     */
    public function show(string|int $uuid): JsonResponse
    {
        $cacheKey = 'model_'.$uuid.'_with_'.implode(',', $this->with);
        $ttl = config('cache.default_ttl');

        $model = $this->cache
            ->remember($cacheKey, $ttl, function () use ($uuid) {
                return $this->repository->getById($uuid, $this->with);
            });

        if (! $model) {
            $this->respondWithError(
                $this->getModelName() . ' not found'
            );
        }

        $resource = $this->makeResource($model);

        return $this->respondWithSuccess($resource);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|int $id): JsonResponse
    {
        if ($this->repository->delete($id)) {
            $resource = $this->makeResource();

            return $this->respondWithSuccess($resource);
        }

        $this->respondWithError($this->getModelName() .' not found');
    }

    /**
     * Returns success response
     */
    protected function respondWithSuccess(JsonResource $resource): JsonResponse
    {
        return $resource->response()->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Throws error and relys on the Exception class to handle the response
     *
     * @throws Exception
     */
    protected function respondWithError(
        string $message,
        int $status = Response::HTTP_NOT_FOUND,
        ?Throwable $previousException = null
    ): void {
        throw new Exception($message, $status, $previousException);
    }
}
