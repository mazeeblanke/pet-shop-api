<?php

namespace Tests\Feature\Models;

use Tests\TestCase;

class ProductTest extends TestCase
{
    /**
     * Resource list fields
     *
    */
    protected array $listFields = [
        // 'brand' => [
        //     'slug',
        //     'title',
        //     'uuid'
        // ],
        // 'category' => [
        //     'slug',
        //     'title',
        //     'uuid'
        // ],
        'category_uuid',
        'description',
        'metadata' => [
            'brand',
            // 'image'
        ],
        'price',
        'title',
        'uuid'
    ];
}
