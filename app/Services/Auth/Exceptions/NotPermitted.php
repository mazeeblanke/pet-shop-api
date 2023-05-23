<?php

namespace App\Services\Auth\Exceptions;

use Exception;
use Illuminate\Http\Response;

class NotPermitted extends Exception
{
    /**
     *
     * @var int
    */
    protected $code = Response::HTTP_UNAUTHORIZED;

    /**
     *
     * @var string
    */
    protected $message = 'Token is not permitted!';
}
