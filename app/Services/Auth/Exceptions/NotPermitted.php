<?php

namespace App\Services\Auth\Exceptions;

use Exception;
use Illuminate\Http\Response;

class NotPermitted extends Exception
{
    protected $code = Response::HTTP_UNAUTHORIZED;

    protected $message = 'Token is not permitted!';
}
