<?php

namespace App\Services\Auth\Exceptions;

use Illuminate\Http\Response;

class InvalidToken extends \Exception
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
    protected $message = 'Unauthorized';
}
