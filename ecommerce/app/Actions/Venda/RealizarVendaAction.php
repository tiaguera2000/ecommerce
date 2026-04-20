<?php

namespace App\Actions\Venda;

use App\Models\Produto;
use App\Models\Venda;
use Illuminate\Support\Collection;

class RealizarVendaAction
{
    public function execute(array $data): array
    {
        $vendas = collect($data['produtos'])->map(function (array $item) use ($data) {
            $produto = Produto::with('compraHistoricos:id,id_produto,quantidade,preco_unitario')
                ->find($item['id']);

            $lucro = round(($produto->preco_venda - $produto->custo_medio) * $item['quantidade'], 2);

            return Venda::create([
                'id_produto'     => $item['id'],
                'cliente'        => $data['cliente'],
                'quantidade'     => $item['quantidade'],
                'preco_unitario' => $produto->preco_venda,
                'lucro'          => $lucro,
            ]);
        });

        return [
            'vendas'  => $vendas,
            'total'   => round($vendas->sum(fn($v) => $v->preco_unitario * $v->quantidade), 2),
            'lucro'   => round($vendas->sum('lucro'), 2),
        ];
    }
}
