<?php

namespace App\Http\Controllers;

use App\Actions\Compra\ListCompraAction;
use App\Actions\Compra\RealizarCompraAction;
use App\Http\Requests\StoreCompraRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompraController extends Controller
{
    public function index(Request $request, ListCompraAction $action): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);

        return response()->json($action->execute($perPage));
    }

    public function store(StoreCompraRequest $request, RealizarCompraAction $action): JsonResponse
    {
        $compras = $action->execute($request->validated());

        return response()->json($compras, 201);
    }
}
