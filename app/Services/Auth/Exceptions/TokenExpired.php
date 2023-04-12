<?php

namespace App\Services\Auth\Exceptions;

use Illuminate\Http\Response;

class TokenExpired extends \Exception
{
    protected $code = Response::HTTP_UNAUTHORIZED;

    protected $message = 'Token has expired!';
}
