<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Venda extends Model
{
    protected $fillable = ['id_produto', 'cliente', 'quantidade', 'preco_unitario', 'lucro', 'cancelada', 'estornada_em'];

    protected $casts = [
        'cancelada'    => 'boolean',
        'estornada_em' => 'datetime',
    ];

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }
}
