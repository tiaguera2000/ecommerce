<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ComprasService, type CompraListItem } from '@/services/compras'
import type { PaginatedResponse } from '@/services/produtos'

const router = useRouter()
const loading = ref(true)
const paginacao = ref<PaginatedResponse<CompraListItem> | null>(null)
const pagina = ref(1)

async function carregar(p = 1) {
  loading.value = true
  try {
    const { data } = await ComprasService.listar(p)
    paginacao.value = data
    pagina.value = p
  } finally {
    loading.value = false
  }
}

onMounted(() => carregar())

function formatCurrency(v: number) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v)
}

function formatDate(d: string) {
  return new Date(d).toLocaleString('pt-BR')
}
</script>

<template>
  <div class="page">
    <div class="page-header">
      <div>
        <h1 class="page-title">🛍️ Compras</h1>
        <p class="page-subtitle">Histórico de todas as compras realizadas.</p>
      </div>
      <button class="btn btn-primary" @click="router.push('/compras/nova')">
        + Nova Compra
      </button>
    </div>

    <div class="table-card">
      <div v-if="loading" class="table-loading">Carregando...</div>

      <table v-else-if="paginacao && paginacao.data.length" class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>Produto</th>
            <th>Fornecedor</th>
            <th>Qtd</th>
            <th>Preço Unit.</th>
            <th>Total Pago</th>
            <th>Data</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="compra in paginacao.data" :key="compra.id">
            <td class="td-id">{{ compra.id }}</td>
            <td>{{ compra.produto?.nome ?? '—' }}</td>
            <td>{{ compra.fornecedor }}</td>
            <td>{{ compra.quantidade }}</td>
            <td>{{ formatCurrency(compra.preco_unitario) }}</td>
            <td class="td-total">{{ formatCurrency(compra.preco_unitario * compra.quantidade) }}</td>
            <td class="td-date">{{ formatDate(compra.created_at) }}</td>
          </tr>
        </tbody>
      </table>

      <div v-else class="table-empty">Nenhuma compra registrada ainda.</div>

      <div v-if="paginacao && paginacao.last_page > 1" class="pagination">
        <button class="btn btn-ghost btn-sm" :disabled="pagina === 1" @click="carregar(pagina - 1)">
          ← Anterior
        </button>
        <span class="pagination-info">{{ pagina }} / {{ paginacao.last_page }}</span>
        <button class="btn btn-ghost btn-sm" :disabled="pagina === paginacao.last_page" @click="carregar(pagina + 1)">
          Próxima →
        </button>
      </div>
    </div>
  </div>
</template>
