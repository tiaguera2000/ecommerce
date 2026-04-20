<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produto extends Model
{
    protected $fillable = ['nome', 'preco_venda', 'quantidade'];

    protected $appends = ['custo_medio'];

    public function compraHistoricos(): HasMany
    {
        return $this->hasMany(CompraHistorico::class, 'id_produto');
    }

    protected function custoMedio(): Attribute
    {
        return Attribute::make(
            get: function () {
                $compras = $this->compraHistoricos;

                $totalQuantidade = $compras->sum('quantidade');

                if ($totalQuantidade === 0) {
                    return 0.0;
                }

                $totalCusto = $compras->sum(fn($c) => $c->quantidade * $c->preco_unitario);

                return round($totalCusto / $totalQuantidade, 2);
            }
        );
    }
}
