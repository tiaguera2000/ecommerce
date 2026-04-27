import { defineStore } from 'pinia'
import { ref } from 'vue'
import { ComprasService, type CompraListItem, type CompraPayload } from '@/services/compras'
import type { PaginatedResponse } from '@/services/produtos'

export const useComprasStore = defineStore('compras', () => {
  const paginacao = ref<PaginatedResponse<CompraListItem> | null>(null)
  const pagina = ref(1)
  const loading = ref(false)

  async function listar(p = 1) {
    loading.value = true
    try {
      const { data } = await ComprasService.listar(p)
      paginacao.value = data
      pagina.value = p
    } finally {
      loading.value = false
    }
  }

  async function realizar(payload: CompraPayload) {
    return await ComprasService.realizar(payload)
  }

  return { paginacao, pagina, loading, listar, realizar }
})
