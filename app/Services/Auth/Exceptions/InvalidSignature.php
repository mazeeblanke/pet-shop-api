<?php

namespace App\Services\Auth\Exceptions;

use Illuminate\Http\Response;

class InvalidSignature extends \Exception
{
    protected $code = Response::HTTP_UNAUTHORIZED;

    protected $message = 'Token signature is invalid!';
}
