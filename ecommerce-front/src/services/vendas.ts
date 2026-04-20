import api from './api'
import type { PaginatedResponse } from './produtos'

export interface VendaItem {
  id: number
  quantidade: number
}

export interface VendaPayload {
  cliente: string
  produtos: VendaItem[]
}

export interface VendaResultItem {
  id: number
  id_produto: number
  cliente: string
  quantidade: number
  preco_unitario: number
  lucro: number
  cancelada: boolean
}

export interface VendaResponse {
  vendas: VendaResultItem[]
  total: number
  lucro: number
}

export interface VendaListItem extends VendaResultItem {
  produto: { id: number; nome: string }
  estornada_em: string | null
  created_at: string
}

export const VendasService = {
  listar(page = 1, perPage = 15) {
    return api.get<PaginatedResponse<VendaListItem>>('/vendas', {
      params: { page, per_page: perPage },
    })
  },

  realizar(data: VendaPayload) {
    return api.post<VendaResponse>('/vendas', data)
  },

  estornar(id: number) {
    return api.patch(`/vendas/${id}/estornar`)
  },
}
