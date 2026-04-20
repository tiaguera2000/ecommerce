<?php

namespace App\Actions\Venda;

use App\Models\Venda;
use Illuminate\Validation\ValidationException;

class EstornarVendaAction
{
    public function execute(Venda $venda): Venda
    {
        if ($venda->cancelada) {
            throw ValidationException::withMessages([
                'venda' => 'Esta venda já foi estornada.',
            ]);
        }

        $venda->update([
            'cancelada'    => true,
            'estornada_em' => now(),
        ]);

        return $venda->fresh();
    }
}
