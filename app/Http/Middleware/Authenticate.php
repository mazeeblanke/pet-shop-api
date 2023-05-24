<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\Middleware;

class Authenticate extends Middleware
{
    /**
     * The authentication factory instance.
     */
    protected \Illuminate\Contracts\Auth\Factory $auth;

    /**
     * Create a new middleware instance.
     *
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next, ?string $admin = null): JsonResponse
    {
        if (! $this->auth->guard()->check()) {
            $this->handleError();
        }

        $user = $request->user();

        if ($admin && $user && ! $user->is_admin) {
            $this->handleError();
        }

        return $next($request);
    }

    protected function handleError(): void
    {
        throw new Exception(
            Response::$statusTexts[Response::HTTP_UNAUTHORIZED],
            Response::HTTP_UNAUTHORIZED
        );
    }
}
