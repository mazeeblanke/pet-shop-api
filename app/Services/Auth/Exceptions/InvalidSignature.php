<?php

namespace App\Services\Auth\Exceptions;

use Illuminate\Http\Response;

class InvalidSignature extends \Exception
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
    protected $message = 'Token signature is invalid!';
}
