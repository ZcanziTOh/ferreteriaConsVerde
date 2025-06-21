<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/consultar-ruc', [ApiController::class, 'consultarRuc']);
    Route::get('/consultar-dni', [ApiController::class, 'consultarDni']);
});