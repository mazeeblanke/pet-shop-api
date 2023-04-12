<?php

namespace App\Services\Auth\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface JWTAuthenticatable extends Authenticatable
{
    public function getClaims();
}
