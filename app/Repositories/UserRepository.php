<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends Repository
{
    protected string $modelIdKey = 'uuid';

    public function __construct(User $model)
    {
        $this->model = $model;
    }
}
