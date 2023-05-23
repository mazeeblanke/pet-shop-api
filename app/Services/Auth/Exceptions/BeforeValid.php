<?php

namespace App\Services\Auth\Exceptions;

use Illuminate\Http\Response;

class BeforeValid extends \Exception
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
    protected $message = 'Token has not been issued, not valid yet!';
}
