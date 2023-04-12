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
     * @var array
    */
    protected $listFields = [
        'title',
        'uuid',
        'slug'
    ];
}
