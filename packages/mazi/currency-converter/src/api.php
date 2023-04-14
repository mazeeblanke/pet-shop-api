<?php

use Illuminate\Support\Facades\Route;
use Mazi\CurrencyConverter\Http\Controllers\Controller;

Route::group(['prefix' => 'api/v1'], static function (): void {
    Route::get('convert-currency', [Controller::class, 'convert']);
});
