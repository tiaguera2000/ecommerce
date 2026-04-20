<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { ProdutosService, type Produto } from '@/services/produtos'
import { VendasService, type VendaResponse } from '@/services/vendas'

const router = useRouter()

const produtos = ref<Produto[]>([])
const loading = ref(false)
const loadingProdutos = ref(true)
const errors = ref<Record<string, string>>({})
const resultado = ref<VendaResponse | null>(null)

const cliente = ref('')
const itens = ref([{ produto_id: '', quantidade: 1 }])

onMounted(async () => {
  try {
    const { data } = await ProdutosService.listar(1, 100)
    produtos.value = data.data.filter(p => p.quantidade > 0)
  } finally {
    loadingProdutos.value = false
  }
})

function addItem() {
  itens.value.push({ produto_id: '', quantidade: 1 })
}

function removeItem(index: number) {
  itens.value.splice(index, 1)
}

function getProduto(id: string | number): Produto | undefined {
  return produtos.value.find(p => p.id === Number(id))
}

const totalEstimado = computed(() => {
  return itens.value.reduce((sum, item) => {
    const p = getProduto(item.produto_id)
    return sum + (p ? p.preco_venda * item.quantidade : 0)
  }, 0)
})

async function handleSubmit() {
  errors.value = {}
  loading.value = true
  resultado.value = null

  const itensFiltrados = itens.value.filter(i => i.produto_id)

  try {
    const { data } = await VendasService.realizar({
      cliente: cliente.value,
      produtos: itensFiltrados.map(i => ({
        id: Number(i.produto_id),
        quantidade: Number(i.quantidade),
      })),
    })
    resultado.value = data
    cliente.value = ''
    itens.value = [{ produto_id: '', quantidade: 1 }]
  } catch (err: any) {
    if (err.response?.status === 422) {
      const apiErrors = err.response.data.errors as Record<string, string[]>
      Object.entries(apiErrors).forEach(([field, msgs]) => {
        errors.value[field] = msgs[0]
      })
    }
  } finally {
    loading.value = false
  }
}

function formatCurrency(value: number) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value)
}
</script>

<template>
  <div class="page">
    <div class="page-header">
      <div>
        <h1 class="page-title">💰 Nova Venda</h1>
        <p class="page-subtitle">Registre uma nova venda para um cliente.</p>
      </div>
      <button class="btn btn-ghost" @click="router.push('/vendas')">← Voltar</button>
    </div>

    <!-- Resultado da venda -->
    <div v-if="resultado" class="resultado-card">
      <h2>✅ Venda realizada com sucesso!</h2>
      <div class="resultado-valores">
        <div class="resultado-item">
          <span>Total da venda</span>
          <strong>{{ formatCurrency(resultado.total) }}</strong>
        </div>
        <div class="resultado-item lucro">
          <span>Lucro obtido</span>
          <strong>{{ formatCurrency(resultado.lucro) }}</strong>
        </div>
      </div>
      <button class="btn btn-primary" @click="resultado = null">Nova Venda</button>
    </div>

    <div v-else class="card form-card">
      <form @submit.prevent="handleSubmit">

        <!-- Cliente -->
        <div class="form-group">
          <label>Nome do Cliente</label>
          <input
            v-model="cliente"
            type="text"
            placeholder="Ex: João Silva"
            :class="{ 'input-error': errors.cliente }"
          />
          <span v-if="errors.cliente" class="field-error">{{ errors.cliente }}</span>
        </div>

        <!-- Produtos -->
        <div class="form-section-title">Produtos</div>

        <div v-if="loadingProdutos" class="loading-text">Carregando produtos...</div>

        <div v-else>
          <div
            v-for="(item, index) in itens"
            :key="index"
            class="item-row"
          >
            <div class="form-group item-select">
              <label>Produto</label>
              <select
                v-model="item.produto_id"
                :class="{ 'input-error': errors[`produtos.${index}.id`] }"
              >
                <option value="">Selecione...</option>
                <option
                  v-for="p in produtos"
                  :key="p.id"
                  :value="p.id"
                >
                  {{ p.nome }} — {{ formatCurrency(p.preco_venda) }} (estoque: {{ p.quantidade }})
                </option>
              </select>
              <span v-if="errors[`produtos.${index}.id`]" class="field-error">
                {{ errors[`produtos.${index}.id`] }}
              </span>
            </div>

            <div class="form-group item-qty">
              <label>Quantidade</label>
              <input
                v-model.number="item.quantidade"
                type="number"
                min="1"
                :max="getProduto(item.produto_id)?.quantidade ?? 9999"
                :class="{ 'input-error': errors[`produtos.${index}.quantidade`] }"
              />
              <span v-if="errors[`produtos.${index}.quantidade`]" class="field-error">
                {{ errors[`produtos.${index}.quantidade`] }}
              </span>
            </div>

            <div class="form-group item-subtotal">
              <label>Subtotal</label>
              <span class="subtotal-value">
                {{ item.produto_id ? formatCurrency((getProduto(item.produto_id)?.preco_venda ?? 0) * item.quantidade) : '—' }}
              </span>
            </div>

            <button
              type="button"
              class="btn-remove"
              :disabled="itens.length === 1"
              @click="removeItem(index)"
              title="Remover item"
            >✕</button>
          </div>

          <button type="button" class="btn btn-ghost btn-add" @click="addItem">
            + Adicionar Produto
          </button>
        </div>

        <!-- Total estimado -->
        <div v-if="totalEstimado > 0" class="total-row">
          <span>Total estimado:</span>
          <strong>{{ formatCurrency(totalEstimado) }}</strong>
        </div>

        <div class="form-actions">
          <button
            type="submit"
            class="btn btn-primary"
            :disabled="loading || loadingProdutos"
          >
            {{ loading ? 'Processando...' : 'Finalizar Venda' }}
          </button>
        </div>

      </form>
    </div>
  </div>
</template>
