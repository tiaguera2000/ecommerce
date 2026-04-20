<?php

namespace App\Http\Controllers;

use App\Actions\Venda\EstornarVendaAction;
use App\Actions\Venda\ListVendaAction;
use App\Actions\Venda\RealizarVendaAction;
use App\Http\Requests\StoreVendaRequest;
use App\Models\Venda;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendaController extends Controller
{
    public function index(Request $request, ListVendaAction $action): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);

        return response()->json($action->execute($perPage));
    }

    public function store(StoreVendaRequest $request, RealizarVendaAction $action): JsonResponse
    {
        return response()->json($action->execute($request->validated()), 201);
    }

    public function estornar(Venda $venda, EstornarVendaAction $action): JsonResponse
    {
        return response()->json($action->execute($venda));
    }
}
