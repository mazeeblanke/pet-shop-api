<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthenticateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends AuthController
{
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
    public function login(AuthenticateRequest $request): JsonResponse
    {
        $response = parent::login($request);

        if (Optional(request()->user())->is_admin) {
            $this->respondWithError('Failed to authenticate user', Response::HTTP_UNAUTHORIZED);
        }

        return $response;
    }

    /**
     * store a resource.
     *
     * @OA\Post(
     *     path="/api/v1/user/create",
     *     tags={"User"},
     *     summary="Create a User account",
     *     description="Users API endpoint",
     *     operationId="user.store",
     *     @OA\RequestBody(
     *         description="User object",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"first_name", "last_name", "email", "password", "password_confirmation", "address", "phone_number"},
     *                 @OA\Property(
     *                     property="first_name",
     *                     type="string",
     *                     description="User firstname",
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     type="string",
     *                     description="User lastname",
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="User email",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="User password"
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     type="string",
     *                     description="User password"
     *                 ),
     *                 @OA\Property(
     *                     property="avatar",
     *                     type="string",
     *                     description="Avatar image UUID"
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     type="string",
     *                     description="User main address"
     *                 ),
     *                 @OA\Property(
     *                     property="phone_number",
     *                     type="string",
     *                     description="User main phone number"
     *                 ),
     *                 @OA\Property(
     *                     property="is_marketing",
     *                     type="string",
     *                     description="User marketing preferences"
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
    public function store(): JsonResponse
    {
        return parent::store();
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
    public function logout(): JsonResponse
    {
        return parent::logout();
    }

    protected function getExtraData(Request $request): array
    {
        return [
            'password' => bcrypt($request->password),
            'is_admin' => 0,
            'is_marketing' => (bool) $request->is_marketing,
        ];
    }
}
