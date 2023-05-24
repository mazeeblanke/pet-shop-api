<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthenticateRequest;
use App\Services\Auth\JWT;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function login(AuthenticateRequest $request): JsonResponse
    {
        $user = null;
        $credentials = $request->only(['email', 'password']);

        if (auth()->guard()->attempt($credentials)) {
            $user = auth()->guard()->user();
        }

        if (! $user) {
            $this->respondWithError(
                Response::$statusTexts[Response::HTTP_UNAUTHORIZED],
                422
            );
        }

        $this->repository->update($user->uuid, [
            'last_login_at' => date('Y-m-d H:i:s'),
        ]);

        $resource = $this->makeResource([
            'token' => JWT::getAccessToken(),
        ]);

        return $this->respondWithSuccess($resource);
    }

    public function logout(): JsonResponse
    {
        auth()->guard()->logout();

        $resource = $this->makeResource();

        return $this->respondWithSuccess($resource);
    }
}
