<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\VendaController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::apiResource('produtos', ProdutoController::class)->only(['index', 'store', 'update']);
    Route::apiResource('compras', CompraController::class)->only(['index', 'store']);
    Route::apiResource('vendas', VendaController::class)->only(['index', 'store']);
    Route::patch('vendas/{venda}/estornar', [VendaController::class, 'estornar']);
});
