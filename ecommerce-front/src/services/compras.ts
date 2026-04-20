import api from './api'
import type { PaginatedResponse } from './produtos'

export interface CompraListItem {
  id: number
  id_produto: number
  produto: { id: number; nome: string }
  quantidade: number
  preco_unitario: number
  fornecedor: string
  created_at: string
}

export interface CompraPayload {
  fornecedor: string
  produtos: { id: number; quantidade: number; preco_unitario: number }[]
}

export const ComprasService = {
  listar(page = 1, perPage = 15) {
    return api.get<PaginatedResponse<CompraListItem>>('/compras', {
      params: { page, per_page: perPage },
    })
  },

  realizar(data: CompraPayload) {
    return api.post('/compras', data)
  },
}
