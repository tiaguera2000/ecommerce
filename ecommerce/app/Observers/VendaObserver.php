<?php

namespace App\Observers;

use App\Models\Produto;
use App\Models\Venda;
use App\Models\VendaHistorico;

class VendaObserver
{
    public function created(Venda $venda): void
    {
        VendaHistorico::create([
            'id_produto'     => $venda->id_produto,
            'cliente'        => $venda->cliente,
            'quantidade'     => $venda->quantidade,
            'preco_unitario' => $venda->preco_unitario,
            'lucro'          => $venda->lucro,
        ]);

        Produto::where('id', $venda->id_produto)
            ->decrement('quantidade', $venda->quantidade);
    }

    public function updated(Venda $venda): void
    {
        if ($venda->wasChanged('cancelada') && $venda->cancelada) {
            Produto::where('id', $venda->id_produto)
                ->increment('quantidade', $venda->quantidade);
        }
    }
}
