<?php

namespace App\Actions\Compra;

use App\Models\Compra;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListCompraAction
{
    public function execute(int $perPage = 15): LengthAwarePaginator
    {
        return Compra::select('id', 'id_produto', 'quantidade', 'preco_unitario', 'fornecedor', 'created_at')
            ->with('produto:id,nome')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}
