<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthenticateRequest;
use Illuminate\Http\Response;

class UserController extends AuthController
{
    public function getExtraData(): array
    {
        return [
            'password' => bcrypt(request()->password),
        ];
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user/login",
     *     tags={"User"},
     *     summary="Login an User account",
     *     description="Users API endpoint",
     *     operationId="user.login",
     *     @OA\RequestBody(
     *         description="User object",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"email", "password"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="User email",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="User password"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ok",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     ),
     * )
     */
    public function login(AuthenticateRequest $request)
    {
        $response = parent::login($request);

        if (request()->user()->is_admin) {
            return $this->respondWithError('Failed to authenticate user', Response::HTTP_UNAUTHORIZED);
        }

        return $response;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/user/logout",
     *     tags={"User"},
     *     summary="Logout an User account",
     *     description="Users API endpoint",
     *     operationId="user.logout",
     *     @OA\Response(
     *         response=200,
     *         description="ok",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     ),
     * )
     */
    public function logout()
    {
        return parent::logout();
    }
}
