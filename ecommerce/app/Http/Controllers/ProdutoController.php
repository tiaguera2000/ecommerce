<?php

namespace App\Http\Controllers;

use App\Actions\Produto\EditProdutoAction;
use App\Actions\Produto\ListProdutoAction;
use App\Actions\Produto\StoreProdutoAction;
use App\Http\Requests\StoreProdutoRequest;
use App\Http\Requests\UpdateProdutoRequest;
use App\Models\Produto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function index(Request $request, ListProdutoAction $action): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);

        return response()->json($action->execute($perPage));
    }

    public function store(StoreProdutoRequest $request, StoreProdutoAction $action): JsonResponse
    {
        $produto = $action->execute($request->validated());

        return response()->json($produto, 201);
    }

    public function show(Produto $produto)
    {
        //
    }

    public function update(UpdateProdutoRequest $request, Produto $produto, EditProdutoAction $action): JsonResponse
    {
        return response()->json($action->execute($produto, $request->validated()));
    }

    public function destroy(Produto $produto)
    {
        //
    }
}
