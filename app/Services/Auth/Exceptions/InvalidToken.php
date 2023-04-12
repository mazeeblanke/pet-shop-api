<?php

namespace App\Services\Auth\Exceptions;

use Illuminate\Http\Response;

class InvalidToken extends \Exception
{
    protected $code = Response::HTTP_UNAUTHORIZED;

    protected $message = 'Unauthorized';
}
