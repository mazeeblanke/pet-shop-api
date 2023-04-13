<?php

use Illuminate\Support\Facades\Route;
use Mazi\CurrencyConverter\Http\Controllers\Controller;

Route::group(['prefix' => 'api/v1'], function() {
    Route::get('convert-currency/{amount}/{currency}', [Controller::class, 'convert']);
});

