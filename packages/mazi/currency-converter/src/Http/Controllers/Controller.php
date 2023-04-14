<?php

namespace Mazi\CurrencyConverter\Http\Controllers;

use Illuminate\Foundation\Application;
use Illuminate\Routing\Controller as BaseController;
use Mazi\CurrencyConverter\Contracts\Converter as ContractsConverter;
use Mazi\CurrencyConverter\Http\Requests\ConversionRequest;

class Controller extends BaseController
{
    /**
     * /**
     *   Convert Currency.
     *
     *   @OA\Get(
     *     path="/api/v1/convert-currency",
     *     tags={"CurrencyConverter"},
     *     summary="Exchange Currency Rate",
     *     operationId="convert-currency",
     *     @OA\Parameter(
     *         name="amount",
     *         in="query",
     *         description="",
     *         required=true,
     *         @OA\Schema(
     *             default="",
     *             type="number",
     *             format="float"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="currency",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             default="EUR",
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
    public function convert(ConversionRequest $request, Application $app)
    {
        $amount = $request->amount;
        $currency = $request->currency;

        // handle conversion
        $converter = $app->make(ContractsConverter::class);

        return $converter->change(
            $amount,
            $currency
        );
    }
}
