<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useProdutosStore } from '@/stores/produtos'

const router = useRouter()
const produtosStore = useProdutosStore()

const form = ref({ nome: '', preco_venda: '' })
const errors = ref<Record<string, string>>({})
const loading = ref(false)
const sucesso = ref(false)

async function handleSubmit() {
  errors.value = {}
  loading.value = true
  sucesso.value = false

  try {
    await produtosStore.cadastrar({
      nome: form.value.nome,
      preco_venda: Number(form.value.preco_venda),
    })
    sucesso.value = true
    form.value = { nome: '', preco_venda: '' }
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
</script>

<template>
  <div class="page">
    <div class="page-header">
      <div>
        <h1 class="page-title">📦 Cadastrar Produto</h1>
        <p class="page-subtitle">Adicione um novo produto ao estoque.</p>
      </div>
      <button class="btn btn-ghost" @click="router.push('/produtos')">← Voltar</button>
    </div>

    <div class="card form-card">
      <div v-if="sucesso" class="alert alert-success">
        ✅ Produto cadastrado com sucesso!
      </div>

      <form @submit.prevent="handleSubmit">
        <div class="form-group">
          <label>Nome do Produto</label>
          <input
            v-model="form.nome"
            type="text"
            placeholder="Ex: Camiseta Polo"
            :class="{ 'input-error': errors.nome }"
          />
          <span v-if="errors.nome" class="field-error">{{ errors.nome }}</span>
        </div>

        <div class="form-group">
          <label>Preço de Venda (R$)</label>
          <input
            v-model="form.preco_venda"
            type="number"
            step="0.01"
            min="0.01"
            placeholder="0,00"
            :class="{ 'input-error': errors.preco_venda }"
          />
          <span v-if="errors.preco_venda" class="field-error">{{ errors.preco_venda }}</span>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary" :disabled="loading">
            {{ loading ? 'Salvando...' : 'Cadastrar Produto' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
