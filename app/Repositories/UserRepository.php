<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserRepository extends Repository
{
    protected string $modelIdKey = 'uuid';

    public function __construct(User $model)
    {
        $this->model = $model;
    }
}
