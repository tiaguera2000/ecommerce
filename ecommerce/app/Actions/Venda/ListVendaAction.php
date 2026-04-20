<?php

namespace App\Actions\Venda;

use App\Models\Venda;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListVendaAction
{
    public function execute(int $perPage = 15): LengthAwarePaginator
    {
        return Venda::select('id', 'id_produto', 'cliente', 'quantidade', 'preco_unitario', 'lucro', 'cancelada', 'estornada_em', 'created_at')
            ->with('produto:id,nome')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}
