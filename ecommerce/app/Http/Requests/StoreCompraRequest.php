<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fornecedor'                        => ['required', 'string', 'min:3'],
            'produtos'                          => ['required', 'array', 'min:1'],
            'produtos.*.id'                     => ['required', 'integer', 'exists:produtos,id'],
            'produtos.*.quantidade'             => ['required', 'integer', 'min:1'],
            'produtos.*.preco_unitario'         => ['required', 'numeric', 'gt:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'fornecedor.required'               => 'O fornecedor é obrigatório.',
            'fornecedor.min'                    => 'O nome do fornecedor deve ter pelo menos 3 caracteres.',
            'produtos.required'                 => 'Informe ao menos um produto.',
            'produtos.min'                      => 'Informe ao menos um produto.',
            'produtos.*.id.required'            => 'O id do produto é obrigatório.',
            'produtos.*.id.exists'              => 'Produto não encontrado.',
            'produtos.*.quantidade.required'    => 'A quantidade é obrigatória.',
            'produtos.*.quantidade.min'         => 'A quantidade deve ser maior que zero.',
            'produtos.*.preco_unitario.required' => 'O preço unitário é obrigatório.',
            'produtos.*.preco_unitario.gt'      => 'O preço unitário deve ser maior que zero.',
        ];
    }
}
