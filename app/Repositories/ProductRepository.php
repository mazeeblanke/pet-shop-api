<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository extends Repository
{
    protected string $modelIdKey = 'uuid';

    public function __construct(Product $model)
    {
        $this->model = $model;
    }
}
