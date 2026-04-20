<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProdutoRequest extends FormRequest
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
        $produtoId = $this->route('produto')?->id;

        return [
            'nome' => ['sometimes', 'string', 'min:3', "unique:produtos,nome,{$produtoId}"],
            'preco_venda' => ['sometimes', 'numeric', 'gt:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'nome.unique' => 'Já existe um produto com este nome.',
            'preco_venda.numeric' => 'O preço de venda deve ser um número.',
            'preco_venda.gt' => 'O preço de venda deve ser maior que zero.',
        ];
    }
}
