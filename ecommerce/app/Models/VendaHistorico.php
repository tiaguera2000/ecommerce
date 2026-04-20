<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendaHistorico extends Model
{
    protected $fillable = ['id_produto', 'cliente', 'quantidade', 'preco_unitario', 'lucro'];

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }
}
