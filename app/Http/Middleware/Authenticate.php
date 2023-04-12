<?php

namespace App\Http\Middleware;

use Exception;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\Middleware;

class Authenticate extends Middleware
{
    /**
     * The authentication factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     *
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, $next, ?string $admin = null)
    {
        if (! $this->auth->guard()->check()) {
            $this->handleError();
        }

        $user = auth()->guard()->user();

        if ($admin && ! $user->is_admin) {
            $this->handleError();
        }

        return $next($request);
    }

    protected function handleError()
    {
        throw new Exception(
            Response::$statusTexts[Response::HTTP_UNAUTHORIZED],
            Response::HTTP_UNAUTHORIZED
        );
    }
}
