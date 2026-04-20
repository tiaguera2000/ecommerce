<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompraHistorico extends Model
{
    protected $fillable = ['id_produto', 'quantidade', 'preco_unitario', 'fornecedor'];

    public function produto()
    {
        return $this->belongsTo(Produto::class, "id_produto");
    }
}
