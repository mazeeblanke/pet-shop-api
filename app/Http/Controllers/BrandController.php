<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     *  @OA\Get(
     *     path="/api/v1/brands",
     *     tags={"Brands"},
     *     summary="List all brands",
     *     description="Brands API endpoint",
     *     operationId="brands.index",
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
     */
    public function store(StoreBrandRequest $request)
    {
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrandRequest $request, Brand $brand)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
    }
}
