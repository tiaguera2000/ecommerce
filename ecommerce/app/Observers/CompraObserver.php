<?php

namespace App\Observers;

use App\Models\Compra;
use App\Models\CompraHistorico;
use App\Models\Produto;

class CompraObserver
{
    public function created(Compra $compra): void
    {
        CompraHistorico::create([
            'id_produto'     => $compra->id_produto,
            'quantidade'     => $compra->quantidade,
            'preco_unitario' => $compra->preco_unitario,
            'fornecedor'     => $compra->fornecedor,
        ]);

        Produto::where('id', $compra->id_produto)
            ->increment('quantidade', $compra->quantidade);
    }
}
