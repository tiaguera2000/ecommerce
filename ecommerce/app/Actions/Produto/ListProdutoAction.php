<?php

namespace App\Actions\Produto;

use App\Models\Produto;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListProdutoAction
{
    public function execute(int $perPage = 15): LengthAwarePaginator
    {
        return Produto::select('id', 'nome', 'preco_venda', 'quantidade')
            ->with('compraHistoricos:id,id_produto,quantidade,preco_unitario')
            ->orderBy('nome')
            ->paginate($perPage);
    }
}
