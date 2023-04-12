<?php

namespace Tests\Feature\Models;

use Tests\TestCase;

class BrandTest extends TestCase
{
    /**
     * Resource list fields
     *
    */
    protected array $listFields = [
        'title',
        'uuid',
        'slug'
    ];

    /**
     * Valid request input
     *
     */
    protected array $validInput = [
        'title' => 'test'
    ];
}
