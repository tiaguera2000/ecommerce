import api from './api'

export interface Produto {
  id: number
  nome: string
  preco_venda: number
  custo_medio: number
  quantidade: number
}

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export const ProdutosService = {
  listar(page = 1, perPage = 15) {
    return api.get<PaginatedResponse<Produto>>('/produtos', {
      params: { page, per_page: perPage },
    })
  },

  cadastrar(data: { nome: string; preco_venda: number }) {
    return api.post<Produto>('/produtos', data)
  },

  atualizar(id: number, data: Partial<{ nome: string; preco_venda: number }>) {
    return api.put<Produto>(`/produtos/${id}`, data)
  },
}
