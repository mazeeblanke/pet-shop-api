<?php

namespace Tests\Feature\Models;

use App\Models\Brand;
use App\Models\Category;
use Tests\TestCase;

class ProductTest extends TestCase
{
    /**
     * Resource list fields
     *
    */
    protected array $listFields = [
        'brand' => [
            'slug',
            'title',
            'uuid',
        ],
        'category' => [
            'slug',
            'title',
            'uuid',
        ],
        'category_uuid',
        'description',
        'metadata' => [
            'brand',
            // 'image'
        ],
        'price',
        'title',
        'uuid',
    ];

    /**
     * Product filters
     *
    */
    protected array $filters = [
        'title',
        'price',
        'category' => [
            'uuid',
        ],
        'brand' => [
            'uuid',
        ],
    ];

    /**
     * Set up product test
     *
     */
    public function setUp(): void
    {
        parent::setUp();
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();
        $this->validInput = [
            'title' => 'test title',
            'price' => 12.99,
            'description' => 'test description',
            'category_uuid' => $category->uuid,
            'metadata' => json_encode([
                'image' => 'string',
                'brand' => $brand->uuid,
            ]),
        ];
    }
}
