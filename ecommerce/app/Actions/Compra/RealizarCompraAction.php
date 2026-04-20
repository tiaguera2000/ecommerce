<?php

namespace App\Actions\Compra;

use App\Models\Compra;
use Illuminate\Support\Collection;

class RealizarCompraAction
{
    public function execute(array $data): Collection
    {
        return collect($data['produtos'])->map(function (array $item) use ($data) {
            return Compra::create([
                'id_produto'     => $item['id'],
                'quantidade'     => $item['quantidade'],
                'preco_unitario' => $item['preco_unitario'],
                'fornecedor'     => $data['fornecedor'],
            ]);
        });
    }
}
