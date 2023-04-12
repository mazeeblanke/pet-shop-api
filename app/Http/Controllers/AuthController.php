<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthenticateRequest;
use App\Services\Auth\JWT;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function getExtraData(): array
    {
        return [
            'password' => bcrypt(request()->password),
        ];
    }

    public function login(AuthenticateRequest $request)
    {
        $user = null;
        $credentials = $request->only(['email', 'password']);

        if (auth()->guard()->attempt($credentials)) {
            $user = auth()->guard()->user();
        }

        if (! $user) {
            return $this->respondWithError(
                Response::$statusTexts[Response::HTTP_UNAUTHORIZED],
                422
            );
        }

        $this->repository->update($user->uuid, [
            'last_login_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->respondWithSuccess(new $this->resource([
            'token' => JWT::getAccessToken(),
        ]));
    }

    public function logout()
    {
        auth()->guard()->logout();

        return $this->respondWithSuccess(new $this->resource([]));
    }
}
