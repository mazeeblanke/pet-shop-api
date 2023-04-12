<?php

namespace App\Services\Auth\Exceptions;

use Illuminate\Http\Response;

class BeforeValid extends \Exception
{
    protected $code = Response::HTTP_UNAUTHORIZED;

    protected $message = 'Token has not been issued, not valid yet!';
}
