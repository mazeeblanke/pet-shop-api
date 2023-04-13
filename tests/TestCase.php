<?php

namespace Tests;

use App\Models\User;
use App\Services\Auth\JWT;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    /**
     * Factory to be used in test
     *
    */
    protected Factory $factory;

    /**
     * Resource name
     */
    protected string $resource;

    /**
     * Model base name
     *
     */
    protected string $modelName;

    /**
     * Resource list fields
     *
     */
    protected array $listFields = [];

    /**
     * default model key
     *
     */
    protected string $modelIdKey = 'uuid';

    /**
     * Resource filters
     *
    */
    protected array $filters = [];

    /**
     * Test user
     *
     */
    protected User $user;

    /**
     * Token string
     *
     */
    protected string $token;

    /**
     * Admin token string
     *
     */
    protected string $adminToken;

    /**
     * Valid request input
     *
     */
    protected array $validInput = [];

    /**
     * Invalid request input
     *
     */
    protected array $invalidInput = [];

    /**
     * Error msgs
     *
     */
    protected array $errorMsgs = [];

    /**
     * Resource create fields
     *
    */
    protected array $createFields = [];

    /**
     * Set up tests
     *
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->factory = $this->getModelClass()::factory();
        $this->resource = $this->getResource();
        $this->setUser();
    }

    protected function setUser()
    {
        $this->user = User::factory()->create();
        auth()->login($this->user);
        $this->token = JWT::getAccessToken();
        auth()->logout();

        // admin
        $admin = User::factory()->create([
            'email' => config('app.admin_email'),
            'password' => bcrypt(config('app.admin_password')),
            'is_admin' => 1
        ]);
        auth()->login($admin);
        $this->adminToken = JWT::getAccessToken();
        auth()->logout();
    }

    /**
     * Get the model resource singular name
     *
     */
    protected function getResource(): string
    {
        $resource = Str::lower($this->getTableName());
        return Str::singular($resource);
    }

    protected function getHeaders(bool $forAdmin = false): array
    {
        return [
            'Authorization' => 'Bearer ' . ($forAdmin ? $this->adminToken  : $this->token)
        ];
    }

    /**
     * Get the model resource plural name
     *
     */
    protected function getTableName(): string
    {
        return strtolower(Str::plural($this->getBaseName()));
    }

    /**
     * Get the model class name
     *
     */
    protected function getModelClass(): string
    {
        return 'App\\Models\\' . $this->getBaseName();
    }

    /**
     * Get the model base name
     *
     */
    protected function getBaseName(): string
    {
        return $this->modelName ?? preg_replace('/Test/', '', class_basename($this));
    }

    /**
     * Get base url
     *
     */
    protected function getBaseUrl($resource = null): string
    {
        return '/api/v1/' . ($resource ?? $this->resource) . '/';
    }

    /**
     * Get the resource data
     *
     */
    protected function getData(array $resource, bool $ignoreNested = false): array
    {
        $data = [];

        foreach ($this->listFields as $key => $field) {
            if ($ignoreNested) continue;

            if (is_array($field)) {
                $nestedData = [];

                foreach ($field as $nestedField) {
                    if (isset($resource[$key][$nestedField])) {
                        $nestedData[$nestedField] = $resource[$key][$nestedField];
                    } else {
                        $nestedData = null;
                        break;
                    }
                }

                $data[$key] = $nestedData;

            } else {
                $data[$field] = $resource[$field] ?? null;
            }
        }

        return $data;
    }

    /**
     * Test to see all.
     */
    public function test_can_see_all(): void
    {
        $page = 1;
        $limit = 10;
        $itemsCount = 50;
        $resources = $this->factory->count($itemsCount)->create();
        $url = $this->getBaseUrl(Str::plural($this->resource));

        // Test pure listing
        $this->test_listing($page, $limit, $url);

        // Test basic Filtering
        $this->test_basic_filtering($page, $limit, $url, $resources, $itemsCount);

        // Test custom Filtering
        $this->test_custom_filtering($page, $limit, $url, $resources);

        // Test pagination
        $this->test_pagination($page, $limit, $url, $resources);
    }

    /**
     *  Test can delete.
     */
    public function test_can_delete(): void
    {
        $resource = $this->factory->create();

        $this->test_cannot_delete_with_auth($resource);

        $this->test_can_delete_with_auth($resource);
    }

    /**
     *  Test if the correct fields are returned by the resource model.
     */
    public function test_can_show(): void
    {
        $resource = $this->factory->create();
        $response = $this->getJson($this->getBaseUrl() . $resource->created_at);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJsonFragment([
            'success' => 0,
            'data' => [],
            'error' => $this->getBaseName() . ' not found',
            'errors' => [],
            // 'trace' => []
        ]);

        $response = $this->getJson($this->getBaseUrl() . $resource->{$this->modelIdKey});
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'success' => 1,
            'data' => $this->getData($response->json()['data']),
            'error' => null,
            'errors' => [],
            'extra' => []
        ]);
    }

    /**
     * Test to create
     */
    public function test_can_create(): void
    {
        $this->test_cannot_create_without_auth_for_non_user_resource();

        $this->test_cannot_create_with_invalid_input();

        $this->test_can_create_with_valid_input_and_auth();
    }

    protected function test_custom_filtering($page, $limit, $url, $resources, $headers = [])
    {

        if ($this->filters) {
            forEach($this->filters as $filterKey => $filter) {
                if (is_array($filter)) {
                    forEach($filter as $filterVal) {
                        $params = http_build_query([
                            'page' => $page,
                            'limit' => $limit,
                            $filterKey => $resources[0]->{$filterKey}[$filterVal]
                        ]);
                        $response = $this->getJson($url . "?" . $params, $headers);

                        $response->assertJsonCount(1, 'data');
                    }
                } else {
                    $params = http_build_query([
                        'page' => $page,
                        'limit' => $limit,
                        $filter => $resources[0]->{$filter}
                    ]);
                    $response = $this->getJson($url . "?" . $params, $headers);
                    $response->assertJsonCount(1, 'data');
                }
            }
        }
    }

    protected function test_basic_filtering($page, $limit, $url, $resources, $itemsCount, $headers = [])
    {
        $params = http_build_query([
            'page' => $page,
            'sortBy' => 'id',
            'desc' => true,
            'limit' => $limit
        ]);
        $response = $this->getJson($url . "?" . $params, $headers);
        $response->assertJsonFragment([
            'uuid' => $resources[$itemsCount - 2]->uuid
        ]);

        $params = http_build_query([
            'page' => $page,
            'sortBy' => 'wrongcolumn',
            'desc' => true,
            'limit' => $limit
        ]);
        $response = $this->getJson($url . "?" . $params, $headers);
        $response->assertJsonCount($limit, 'data');
    }

    protected function test_listing($page, $limit, $url, $headers = [])
    {
        $params = http_build_query([
            'page' => $page,
            'limit' => $limit
        ]);
        $response = $this->getJson($url . "?" . $params, $headers);
        $response->assertSuccessful();
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => $this->listFields,
            ],
            'first_page_url',
            'from',
            'to',
            'total',
            'per_page',
            'path',
            'links' => [
                '*' => [
                    'active',
                    'label',
                    'url'
                ]
            ],
            'next_page_url',
            'prev_page_url',
            'last_page',
            'last_page_url'
        ]);
    }

    protected function test_pagination($page, $limit, $url, $resources, $headers = [])
    {
        $page = 2;
        $params = http_build_query([
            'page' => $page,
            'limit' => $limit
        ]);
        $response = $this->getJson($url . "?" . $params, $headers);

        $response->assertJsonFragment([
            'uuid' => $resources[$page + $limit]->uuid
        ]);
    }

    protected function test_cannot_delete_with_auth($resource)
    {
        $response = $this->deleteJson($this->getBaseUrl() . $resource->{$this->modelIdKey});
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson([
            'success' => 0,
            'data' => [],
            'error' => Response::$statusTexts[Response::HTTP_UNAUTHORIZED],
            'errors' => [],
            'trace' => []
        ]);
    }

    protected function test_can_delete_with_auth($resource)
    {
        $response = $this->deleteJson($this->getBaseUrl() . $resource->{$this->modelIdKey}, [], $this->getHeaders());
        $response->assertStatus(Response::HTTP_OK);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                "success" => 1,
                "data" => [],
                "error" => null,
                "errors" => [],
                "extra" => []
            ]);
    }

    protected function test_cannot_create_without_auth_for_non_user_resource()
    {
        if ($this->resource != 'user') {
            $response = $this->postJson($this->getBaseUrl() . 'create', $this->invalidInput);
            $response->assertStatus(Response::HTTP_UNAUTHORIZED);
            $response->assertJsonFragment([
                "success" => 0,
                "data" => [],
                "error" => Response::$statusTexts[Response::HTTP_UNAUTHORIZED],
                "errors" => []
            ]);
        }
    }

    protected function test_cannot_create_with_invalid_input()
    {
        $response = $this->postJson(
            $this->getBaseUrl() . 'create',
            $this->invalidInput,
            $this->getHeaders()
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment($this->errorMsgs);
    }

    protected function test_can_create_with_valid_input_and_auth()
    {
        $response = $this->postJson(
            $this->getBaseUrl(). 'create',
            $this->validInput,
            $this->getHeaders()
        );
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'success',
            'data' => $this->createFields,
            'error',
            'errors',
            'extra'
        ]);

        $this->assertDatabaseHas(
            $this->getTableName(),
            $this->getData($response->json()["data"], true)
        );
    }
}
