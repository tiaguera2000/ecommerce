<?php

namespace App\Actions\Produto;

use App\Models\Produto;

class EditProdutoAction
{
    public function execute(Produto $produto, array $data): Produto
    {
        $produto->update($data);

        return $produto->fresh();
    }
}
