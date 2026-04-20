import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AppLayout from '@/layouts/AppLayout.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/LoginView.vue'),
      meta: { guest: true },
    },
    {
      path: '/',
      component: AppLayout,
      meta: { requiresAuth: true },
      redirect: '/produtos',
      children: [
        // Produtos
        {
          path: 'produtos',
          name: 'produtos',
          component: () => import('@/views/produtos/ProdutosListView.vue'),
        },
        {
          path: 'produtos/cadastrar',
          name: 'produtos.cadastrar',
          component: () => import('@/views/produtos/ProdutosCadastrarView.vue'),
        },
        // Vendas
        {
          path: 'vendas',
          name: 'vendas',
          component: () => import('@/views/vendas/VendasListView.vue'),
        },
        {
          path: 'vendas/nova',
          name: 'vendas.nova',
          component: () => import('@/views/vendas/VendasNovaView.vue'),
        },
        // Compras
        {
          path: 'compras',
          name: 'compras',
          component: () => import('@/views/compras/ComprasListView.vue'),
        },
        {
          path: 'compras/nova',
          name: 'compras.nova',
          component: () => import('@/views/compras/ComprasNovaView.vue'),
        },
      ],
    },
  ],
})

router.beforeEach((to) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login' }
  }

  if (to.meta.guest && auth.isAuthenticated) {
    return { name: 'produtos' }
  }
})

export default router
