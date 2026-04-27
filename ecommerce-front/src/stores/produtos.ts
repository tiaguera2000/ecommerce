import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { ProdutosService, type Produto, type PaginatedResponse } from '@/services/produtos'

export const useProdutosStore = defineStore('produtos', () => {
  const paginacao = ref<PaginatedResponse<Produto> | null>(null)
  const todos = ref<Produto[]>([])
  const pagina = ref(1)
  const loading = ref(false)
  const loadingTodos = ref(false)

  const comEstoque = computed(() => todos.value.filter(p => p.quantidade > 0))

  async function listar(p = 1) {
    loading.value = true
    try {
      const { data } = await ProdutosService.listar(p)
      paginacao.value = data
      pagina.value = p
    } finally {
      loading.value = false
    }
  }

  async function buscarTodos() {
    loadingTodos.value = true
    try {
      const { data } = await ProdutosService.listar(1, 100)
      todos.value = data.data
    } finally {
      loadingTodos.value = false
    }
  }

  async function cadastrar(payload: { nome: string; preco_venda: number }) {
    return await ProdutosService.cadastrar(payload)
  }

  return { paginacao, todos, comEstoque, pagina, loading, loadingTodos, listar, buscarTodos, cadastrar }
})
