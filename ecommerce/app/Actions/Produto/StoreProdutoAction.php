<?php

namespace App\Actions\Produto;

use App\Models\Produto;

class StoreProdutoAction
{
    public function execute(array $data): Produto
    {
        return Produto::create([
            'nome' => $data['nome'],
            'preco_venda' => $data['preco_venda'],
            'quantidade' => 0,
        ]);
    }
}
