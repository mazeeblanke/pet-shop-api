<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
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
}
