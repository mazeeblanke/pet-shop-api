<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected array $with = ['category', 'brand'];

    /**
     * Display a listing of the resource.
     *
     *  @OA\Get(
     *     path="/api/v1/products",
     *     tags={"Products"},
     *     summary="List all products",
     *     description="Products API endpoint",
     *     operationId="product.index",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="",
     *         required=false,
     *         @OA\Schema(
     *             default="",
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="",
     *         required=false,
     *         explode=false,
     *         @OA\Schema(
     *             default="",
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         description="",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             default="",
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="desc",
     *         in="query",
     *         description="",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             default="",
     *             type="boolean",
     *             enum={"true", "false"},
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             default="",
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="price",
     *         in="query",
     *         description="",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             default="",
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="brand",
     *         in="query",
     *         description="",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             default="",
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         description="",
     *         required=false,
     *         explode=true,
     *         @OA\Schema(
     *             default="",
     *             type="string",
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
    public function index(Request $request)
    {
        return parent::index($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *     path="/api/v1/product/create",
     *     tags={"Products"},
     *     summary="Create a new product",
     *     description="Products API endpoint",
     *     operationId="product.store",
     *     security={{ "bearerAuth": {} }},
     *     @OA\RequestBody(
     *         description="product object",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"title", "category_uuid", "description", "price", "metadata"},
     *                 @OA\Property(
     *                     property="category_uuid",
     *                     type="string",
     *                     description="Category UUID",
     *                 ),
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     description="Product title",
     *                 ),
     *                 @OA\Property(
     *                     property="price",
     *                     type="integer",
     *                     description="Product price",
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     description="Product description",
     *                 ),
     *                 @OA\Property(
     *                     property="metadata",
     *                     type="object",
     *                     description="Product metadata",
     *                    @OA\Property(property="image", type="string"),
     *                    @OA\Property(property="brand", type="string"),
     *                 ),
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
    public function store()
    {
        return parent::store();
    }

    /**
     * Display the specified resource.
     *
     *  @OA\Get(
     *     path="/api/v1/product/{uuid}",
     *     tags={"Products"},
     *     summary="Fetch a product",
     *     description="Products API endpoint",
     *     operationId="product.show",
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="",
     *         required=true,
     *         @OA\Schema(
     *             default="",
     *             type="string",
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
    public function show($uuid)
    {
        return parent::show($uuid);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): void
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/v1/product/{uuid}",
     *     tags={"Products"},
     *     summary="Delete an existing product",
     *     description="Products API endpoint",
     *     operationId="product.delete",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="",
     *         required=true,
     *         @OA\Schema(
     *             default="",
     *             type="string",
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
    public function destroy($id)
    {
        return parent::destroy($id);
    }
}
