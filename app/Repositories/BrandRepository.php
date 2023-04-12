<?php

namespace App\Repositories;

use App\Models\Brand;

class BrandRepository extends Repository
{
    protected string $modelIdKey = 'uuid';

    public function __construct(Brand $model)
    {
        $this->model = $model;
    }
}
