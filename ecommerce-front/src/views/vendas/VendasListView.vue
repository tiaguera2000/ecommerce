<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useVendasStore } from '@/stores/vendas'
import type { VendaListItem } from '@/services/vendas'

const router = useRouter()
const vendasStore = useVendasStore()

onMounted(() => vendasStore.listar())

async function estornar(venda: VendaListItem) {
  if (!confirm(`Estornar venda #${venda.id} de "${venda.cliente}"?`)) return
  await vendasStore.estornar(venda)
}

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
        <h1 class="page-title">💰 Vendas</h1>
        <p class="page-subtitle">Histórico de todas as vendas realizadas.</p>
      </div>
      <button class="btn btn-primary" @click="router.push('/vendas/nova')">
        + Nova Venda
      </button>
    </div>

    <div class="table-card">
      <div v-if="vendasStore.loading" class="table-loading">Carregando...</div>

      <table v-else-if="vendasStore.paginacao && vendasStore.paginacao.data.length" class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>Produto</th>
            <th>Cliente</th>
            <th>Qtd</th>
            <th>Preço Unit.</th>
            <th>Lucro</th>
            <th>Total</th>
            <th>Status</th>
            <th>Data</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="venda in vendasStore.paginacao.data" :key="venda.id" :class="{ 'row-cancelada': venda.cancelada }">
            <td class="td-id">{{ venda.id }}</td>
            <td>{{ venda.produto?.nome ?? '—' }}</td>
            <td>{{ venda.cliente }}</td>
            <td>{{ venda.quantidade }}</td>
            <td>{{ formatCurrency(venda.preco_unitario) }}</td>
            <td class="td-lucro">{{ formatCurrency(venda.lucro) }}</td>
            <td class="td-total">{{ formatCurrency(venda.preco_unitario * venda.quantidade) }}</td>
            <td>
              <span class="badge" :class="venda.cancelada ? 'badge-cancelada' : 'badge-ok'">
                {{ venda.cancelada ? 'Estornada' : 'Ativa' }}
              </span>
            </td>
            <td class="td-date">{{ formatDate(venda.created_at) }}</td>
            <td>
              <button
                v-if="!venda.cancelada"
                class="btn-action btn-estornar"
                :disabled="vendasStore.estornando === venda.id"
                @click="estornar(venda)"
                title="Estornar venda"
              >
                {{ vendasStore.estornando === venda.id ? '...' : '↩ Estornar' }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-else class="table-empty">Nenhuma venda registrada ainda.</div>

      <div v-if="vendasStore.paginacao && vendasStore.paginacao.last_page > 1" class="pagination">
        <button class="btn btn-ghost btn-sm" :disabled="vendasStore.pagina === 1" @click="vendasStore.listar(vendasStore.pagina - 1)">
          ← Anterior
        </button>
        <span class="pagination-info">{{ vendasStore.pagina }} / {{ vendasStore.paginacao.last_page }}</span>
        <button class="btn btn-ghost btn-sm" :disabled="vendasStore.pagina === vendasStore.paginacao.last_page" @click="vendasStore.listar(vendasStore.pagina + 1)">
          Próxima →
        </button>
      </div>
    </div>
  </div>
</template>
