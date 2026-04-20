<?php

namespace App\Http\Requests;

use App\Models\Produto;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreVendaRequest extends FormRequest
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
            'cliente'                      => ['required', 'string', 'min:3'],
            'produtos'                     => ['required', 'array', 'min:1'],
            'produtos.*.id'                => ['required', 'integer', 'exists:produtos,id'],
            'produtos.*.quantidade'        => ['required', 'integer', 'min:1'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            foreach ($this->input('produtos', []) as $index => $item) {
                $produto = Produto::find($item['id'] ?? null);

                if ($produto && $produto->quantidade < $item['quantidade']) {
                    $validator->errors()->add(
                        "produtos.{$index}.quantidade",
                        "Estoque insuficiente para \"{$produto->nome}\". Disponível: {$produto->quantidade}."
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'cliente.required'                  => 'O nome do cliente é obrigatório.',
            'cliente.min'                       => 'O nome do cliente deve ter pelo menos 3 caracteres.',
            'produtos.required'                 => 'Informe ao menos um produto.',
            'produtos.*.id.required'            => 'O id do produto é obrigatório.',
            'produtos.*.id.exists'              => 'Produto não encontrado.',
            'produtos.*.quantidade.required'    => 'A quantidade é obrigatória.',
            'produtos.*.quantidade.min'         => 'A quantidade deve ser maior que zero.',
        ];
    }
}
