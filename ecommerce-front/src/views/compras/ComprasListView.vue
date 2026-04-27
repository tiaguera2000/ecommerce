<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useComprasStore } from '@/stores/compras'

const router = useRouter()
const comprasStore = useComprasStore()

onMounted(() => comprasStore.listar())

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
      <div v-if="comprasStore.loading" class="table-loading">Carregando...</div>

      <table v-else-if="comprasStore.paginacao && comprasStore.paginacao.data.length" class="table">
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
          <tr v-for="compra in comprasStore.paginacao.data" :key="compra.id">
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

      <div v-if="comprasStore.paginacao && comprasStore.paginacao.last_page > 1" class="pagination">
        <button class="btn btn-ghost btn-sm" :disabled="comprasStore.pagina === 1" @click="comprasStore.listar(comprasStore.pagina - 1)">
          ← Anterior
        </button>
        <span class="pagination-info">{{ comprasStore.pagina }} / {{ comprasStore.paginacao.last_page }}</span>
        <button class="btn btn-ghost btn-sm" :disabled="comprasStore.pagina === comprasStore.paginacao.last_page" @click="comprasStore.listar(comprasStore.pagina + 1)">
          Próxima →
        </button>
      </div>
    </div>
  </div>
</template>
