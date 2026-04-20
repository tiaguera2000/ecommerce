<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { VendasService, type VendaListItem } from '@/services/vendas'
import type { PaginatedResponse } from '@/services/produtos'

const router = useRouter()
const loading = ref(true)
const paginacao = ref<PaginatedResponse<VendaListItem> | null>(null)
const pagina = ref(1)
const estornando = ref<number | null>(null)

async function carregar(p = 1) {
  loading.value = true
  try {
    const { data } = await VendasService.listar(p)
    paginacao.value = data
    pagina.value = p
  } finally {
    loading.value = false
  }
}

async function estornar(venda: VendaListItem) {
  if (!confirm(`Estornar venda #${venda.id} de "${venda.cliente}"?`)) return
  estornando.value = venda.id
  try {
    await VendasService.estornar(venda.id)
    await carregar(pagina.value)
  } finally {
    estornando.value = null
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
        <h1 class="page-title">💰 Vendas</h1>
        <p class="page-subtitle">Histórico de todas as vendas realizadas.</p>
      </div>
      <button class="btn btn-primary" @click="router.push('/vendas/nova')">
        + Nova Venda
      </button>
    </div>

    <div class="table-card">
      <div v-if="loading" class="table-loading">Carregando...</div>

      <table v-else-if="paginacao && paginacao.data.length" class="table">
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
          <tr v-for="venda in paginacao.data" :key="venda.id" :class="{ 'row-cancelada': venda.cancelada }">
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
                :disabled="estornando === venda.id"
                @click="estornar(venda)"
                title="Estornar venda"
              >
                {{ estornando === venda.id ? '...' : '↩ Estornar' }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-else class="table-empty">Nenhuma venda registrada ainda.</div>

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
