<?php

use App\Http\Controllers\Api\V1\ClientController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::apiResource('clients', ClientController::class);
});
