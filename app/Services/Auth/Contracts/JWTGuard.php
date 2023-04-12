<?php

namespace App\Services\Auth\Contracts;

use Illuminate\Contracts\Auth\StatefulGuard;

interface JWTGuard extends StatefulGuard
{
    public function getAccessToken();
}
