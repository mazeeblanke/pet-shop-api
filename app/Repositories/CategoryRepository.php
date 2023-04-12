<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository extends Repository
{
    protected string $modelIdKey = 'uuid';

    public function __construct(Category $model)
    {
        $this->model = $model;
    }
}
