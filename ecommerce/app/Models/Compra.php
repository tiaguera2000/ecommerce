<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    /** @use HasFactory<\Database\Factories\CompraFactory> */
    use HasFactory;
    protected $fillable = ['id_produto', 'quantidade', 'preco_unitario', 'fornecedor'];

    public function produto()
    {
        return $this->belongsTo(Produto::class, "id_produto");
    }
}
