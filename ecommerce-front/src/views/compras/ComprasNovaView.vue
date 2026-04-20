<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { ProdutosService, type Produto } from '@/services/produtos'
import { ComprasService } from '@/services/compras'

const router = useRouter()

const produtos = ref<Produto[]>([])
const loadingProdutos = ref(true)
const loading = ref(false)
const errors = ref<Record<string, string>>({})
const resultado = ref<{ fornecedor: string; itens: ResultadoItem[] } | null>(null)

interface ResultadoItem {
  nome: string
  quantidade: number
  preco_unitario: number
  total: number
}

const fornecedor = ref('')
const itens = ref([{ produto_id: '', quantidade: 1, preco_unitario: '' }])

onMounted(async () => {
  try {
    const { data } = await ProdutosService.listar(1, 100)
    produtos.value = data.data
  } finally {
    loadingProdutos.value = false
  }
})

function getProduto(id: string | number): Produto | undefined {
  return produtos.value.find(p => p.id === Number(id))
}

function addItem() {
  itens.value.push({ produto_id: '', quantidade: 1, preco_unitario: '' })
}

function removeItem(index: number) {
  itens.value.splice(index, 1)
}

const totalGeral = computed(() =>
  itens.value.reduce((sum, item) => {
    const preco = parseFloat(item.preco_unitario as string) || 0
    return sum + preco * item.quantidade
  }, 0),
)

async function handleSubmit() {
  errors.value = {}
  loading.value = true
  resultado.value = null

  const itensFiltrados = itens.value.filter(i => i.produto_id)

  try {
    await ComprasService.realizar({
      fornecedor: fornecedor.value,
      produtos: itensFiltrados.map(i => ({
        id: Number(i.produto_id),
        quantidade: Number(i.quantidade),
        preco_unitario: parseFloat(i.preco_unitario as string),
      })),
    })

    resultado.value = {
      fornecedor: fornecedor.value,
      itens: itensFiltrados.map(i => {
        const p = getProduto(i.produto_id)
        const preco = parseFloat(i.preco_unitario as string)
        return {
          nome: p?.nome ?? '—',
          quantidade: Number(i.quantidade),
          preco_unitario: preco,
          total: preco * Number(i.quantidade),
        }
      }),
    }

    fornecedor.value = ''
    itens.value = [{ produto_id: '', quantidade: 1, preco_unitario: '' }]
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

function formatCurrency(v: number) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v)
}
</script>

<template>
  <div class="page">
    <div class="page-header">
      <div>
        <h1 class="page-title">🛍️ Nova Compra</h1>
        <p class="page-subtitle">Registre uma nova entrada de produtos no estoque.</p>
      </div>
      <button class="btn btn-ghost" @click="router.push('/compras')">← Voltar</button>
    </div>

    <!-- Resultado -->
    <div v-if="resultado" class="resultado-card resultado-compra">
      <h2>✅ Compra registrada com sucesso!</h2>
      <p class="resultado-fornecedor">Fornecedor: <strong>{{ resultado.fornecedor }}</strong></p>

      <table class="table resultado-table">
        <thead>
          <tr>
            <th>Produto</th>
            <th>Qtd</th>
            <th>Preço Unit.</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, i) in resultado.itens" :key="i">
            <td>{{ item.nome }}</td>
            <td>{{ item.quantidade }}</td>
            <td>{{ formatCurrency(item.preco_unitario) }}</td>
            <td class="td-total">{{ formatCurrency(item.total) }}</td>
          </tr>
        </tbody>
      </table>

      <div class="resultado-total">
        <span>Total investido:</span>
        <strong>{{ formatCurrency(resultado.itens.reduce((s, i) => s + i.total, 0)) }}</strong>
      </div>

      <div class="resultado-actions">
        <button class="btn btn-primary" @click="resultado = null">Nova Compra</button>
        <button class="btn btn-ghost" @click="router.push('/compras')">Ver Histórico</button>
      </div>
    </div>

    <!-- Formulário -->
    <div v-else class="card form-card form-card-lg">
      <form @submit.prevent="handleSubmit">

        <!-- Fornecedor -->
        <div class="form-group">
          <label>Fornecedor</label>
          <input
            v-model="fornecedor"
            type="text"
            placeholder="Ex: Distribuidora ABC"
            :class="{ 'input-error': errors.fornecedor }"
          />
          <span v-if="errors.fornecedor" class="field-error">{{ errors.fornecedor }}</span>
        </div>

        <!-- Produtos -->
        <div class="form-section-title">Produtos adquiridos</div>

        <div v-if="loadingProdutos" class="loading-text">Carregando produtos...</div>

        <div v-else>
          <div
            v-for="(item, index) in itens"
            :key="index"
            class="item-row item-row-compra"
          >
            <!-- Produto -->
            <div class="form-group">
              <label>Produto</label>
              <select
                v-model="item.produto_id"
                :class="{ 'input-error': errors[`produtos.${index}.id`] }"
              >
                <option value="">Selecione...</option>
                <option v-for="p in produtos" :key="p.id" :value="p.id">
                  {{ p.nome }}
                </option>
              </select>
              <span v-if="errors[`produtos.${index}.id`]" class="field-error">
                {{ errors[`produtos.${index}.id`] }}
              </span>
            </div>

            <!-- Quantidade -->
            <div class="form-group">
              <label>Quantidade</label>
              <input
                v-model.number="item.quantidade"
                type="number"
                min="1"
                :class="{ 'input-error': errors[`produtos.${index}.quantidade`] }"
              />
              <span v-if="errors[`produtos.${index}.quantidade`]" class="field-error">
                {{ errors[`produtos.${index}.quantidade`] }}
              </span>
            </div>

            <!-- Preço Unitário (custo) -->
            <div class="form-group">
              <label>Custo unitário (R$)</label>
              <input
                v-model="item.preco_unitario"
                type="number"
                step="0.01"
                min="0.01"
                placeholder="0,00"
                :class="{ 'input-error': errors[`produtos.${index}.preco_unitario`] }"
              />
              <span v-if="errors[`produtos.${index}.preco_unitario`]" class="field-error">
                {{ errors[`produtos.${index}.preco_unitario`] }}
              </span>
            </div>

            <!-- Subtotal -->
            <div class="form-group">
              <label>Subtotal</label>
              <span class="subtotal-value">
                {{ item.produto_id && item.preco_unitario
                  ? formatCurrency(parseFloat(item.preco_unitario as string) * item.quantidade)
                  : '—' }}
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

        <!-- Total -->
        <div v-if="totalGeral > 0" class="total-row">
          <span>Total da compra:</span>
          <strong>{{ formatCurrency(totalGeral) }}</strong>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary" :disabled="loading || loadingProdutos">
            {{ loading ? 'Registrando...' : 'Registrar Compra' }}
          </button>
        </div>

      </form>
    </div>
  </div>
</template>
