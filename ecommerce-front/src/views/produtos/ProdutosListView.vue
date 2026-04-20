<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ProdutosService, type Produto, type PaginatedResponse } from '@/services/produtos'

const router = useRouter()
const loading = ref(true)
const paginacao = ref<PaginatedResponse<Produto> | null>(null)
const pagina = ref(1)

async function carregar(p = 1) {
  loading.value = true
  try {
    const { data } = await ProdutosService.listar(p)
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
</script>

<template>
  <div class="page">
    <div class="page-header">
      <div>
        <h1 class="page-title">📦 Produtos</h1>
        <p class="page-subtitle">Lista de todos os produtos cadastrados.</p>
      </div>
      <button class="btn btn-primary" @click="router.push('/produtos/cadastrar')">
        + Novo Produto
      </button>
    </div>

    <div class="table-card">
      <div v-if="loading" class="table-loading">Carregando...</div>

      <table v-else-if="paginacao && paginacao.data.length" class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>Nome</th>
            <th>Preço de Venda</th>
            <th>Custo Médio</th>
            <th>Estoque</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="produto in paginacao.data" :key="produto.id">
            <td class="td-id">{{ produto.id }}</td>
            <td class="td-nome">{{ produto.nome }}</td>
            <td>{{ formatCurrency(produto.preco_venda) }}</td>
            <td>{{ formatCurrency(produto.custo_medio) }}</td>
            <td>
              <span class="badge" :class="produto.quantidade > 0 ? 'badge-ok' : 'badge-zero'">
                {{ produto.quantidade }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-else class="table-empty">Nenhum produto cadastrado ainda.</div>

      <!-- Paginação -->
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
