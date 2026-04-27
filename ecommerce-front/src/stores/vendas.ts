import { defineStore } from 'pinia'
import { ref } from 'vue'
import { VendasService, type VendaListItem, type VendaPayload, type VendaResponse } from '@/services/vendas'
import type { PaginatedResponse } from '@/services/produtos'

export const useVendasStore = defineStore('vendas', () => {
  const paginacao = ref<PaginatedResponse<VendaListItem> | null>(null)
  const pagina = ref(1)
  const loading = ref(false)
  const estornando = ref<number | null>(null)
  const resultado = ref<VendaResponse | null>(null)

  async function listar(p = 1) {
    loading.value = true
    try {
      const { data } = await VendasService.listar(p)
      paginacao.value = data
      pagina.value = p
    } finally {
      loading.value = false
    }
  }

  async function realizar(payload: VendaPayload) {
    const { data } = await VendasService.realizar(payload)
    resultado.value = data
    return data
  }

  async function estornar(venda: VendaListItem) {
    estornando.value = venda.id
    try {
      await VendasService.estornar(venda.id)
      await listar(pagina.value)
    } finally {
      estornando.value = null
    }
  }

  function limparResultado() {
    resultado.value = null
  }

  return { paginacao, pagina, loading, estornando, resultado, listar, realizar, estornar, limparResultado }
})
