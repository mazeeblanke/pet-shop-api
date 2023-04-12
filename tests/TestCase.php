<?php

namespace Tests;

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

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = $this->getModelClass()::factory();
        $this->resource = $this->getResource();
    }

    /**
     * Get the model resource singular name
     *
     * @return  string
     */
    protected function getResource(): string
    {
        $resource = Str::lower($this->getTableName());
        return Str::singular($resource);
    }

    /**
     * Get the model resource plural name
     *
     * @return  string
     */
    protected function getTableName(): string
    {
        return strtolower(Str::plural($this->getBaseName()));
    }

    /**
     * Get the model class name
     *
     * @return  string
     */
    protected function getModelClass(): string
    {
        return 'App\\Models\\' . $this->getBaseName();
    }

    /**
     * Get the model base name
     *
     * @return  string
     */
    protected function getBaseName(): string
    {
        return $this->modelName ?? preg_replace('/Test/', '', class_basename($this));
    }

    /**
     * [getBaseUrl description]
     *
     * @return  string  [return description]
     */
    protected function getBaseUrl($resource = null): string
    {
        return '/api/v1/' . ($resource ?? $this->resource) . '/';
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

        $response = $this->call('GET', $url, [
            'page' => $page,
            'limit' => $limit
        ]);

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

        // Basic Filter
        $response = $this->call('GET', $url, [
            'page' => $page,
            'sortBy' => 'id',
            'desc' => true,
            'limit' => $limit
        ]);
        $response->assertJsonFragment([
            'title' => $resources[$itemsCount - 2]->title
        ]);

        $response = $this->call('GET', $url, [
            'page' => $page,
            'sortBy' => 'wrongcolumn',
            'desc' => true,
            'limit' => $limit
        ]);
        $response->assertJsonCount($limit, 'data');

        // Test Filters
        if ($this->filters) {
            forEach($this->filters as $filterKey => $filter) {
                if (is_array($filter)) {
                    forEach($filter as $filterVal) {
                        $response = $this->call('GET', $url, [
                            'page' => $page,
                            'limit' => $limit,
                            $filterKey => $resources[0]->{$filterKey}[$filterVal]
                        ]);

                        $response->assertJsonCount(1, 'data');
                    }
                } else {
                    $response = $this->call('GET', $url, [
                        'page' => $page,
                        'limit' => $limit,
                        $filter => $resources[0]->{$filter}
                    ]);
                    $response->assertJsonCount(1, 'data');
                }
            }
        }

        $page = 2;
        $response = $this->call('GET', $url, [
            'page' => $page,
            'limit' => $limit
        ]);

       $response->assertJsonFragment([
        'title' => $resources[$page + $limit]->title
       ]);
    }
}
