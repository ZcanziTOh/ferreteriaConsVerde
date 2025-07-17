<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/consultar-dni', [VendedorController::class, 'consultarDni']);
    Route::post('/consultar-ruc', [VendedorController::class, 'consultarRuc']);
});