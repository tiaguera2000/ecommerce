<script setup lang="ts">
import { ref } from 'vue'
import { RouterView, RouterLink, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()

const openMenus = ref<Record<string, boolean>>({
  produtos: false,
  vendas: false,
  compras: false,
})

function toggleMenu(key: string) {
  openMenus.value[key] = !openMenus.value[key]
}

async function handleLogout() {
  await auth.logout()
  router.push('/login')
}
</script>

<template>
  <div class="app-shell">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-brand">
        <span class="brand-icon">🛒</span>
        <span class="brand-name">Ecommerce</span>
      </div>

      <nav class="sidebar-nav">

        <!-- Produtos -->
        <div class="nav-group">
          <button class="nav-group-btn" @click="toggleMenu('produtos')">
            <span class="nav-icon">📦</span>
            <span>Produtos</span>
            <span class="nav-chevron" :class="{ open: openMenus.produtos }">›</span>
          </button>
          <div class="nav-submenu" :class="{ open: openMenus.produtos }">
            <RouterLink to="/produtos/cadastrar" class="nav-sub-item">Cadastrar</RouterLink>
            <RouterLink to="/produtos" class="nav-sub-item">Listar / Editar</RouterLink>
          </div>
        </div>

        <!-- Vendas -->
        <div class="nav-group">
          <button class="nav-group-btn" @click="toggleMenu('vendas')">
            <span class="nav-icon">💰</span>
            <span>Vendas</span>
            <span class="nav-chevron" :class="{ open: openMenus.vendas }">›</span>
          </button>
          <div class="nav-submenu" :class="{ open: openMenus.vendas }">
            <RouterLink to="/vendas/nova" class="nav-sub-item">Nova Venda</RouterLink>
            <RouterLink to="/vendas" class="nav-sub-item">Listar Vendas</RouterLink>
          </div>
        </div>

        <!-- Compras -->
        <div class="nav-group">
          <button class="nav-group-btn" @click="toggleMenu('compras')">
            <span class="nav-icon">🛍️</span>
            <span>Compras</span>
            <span class="nav-chevron" :class="{ open: openMenus.compras }">›</span>
          </button>
          <div class="nav-submenu" :class="{ open: openMenus.compras }">
            <RouterLink to="/compras/nova" class="nav-sub-item">Nova Compra</RouterLink>
            <RouterLink to="/compras" class="nav-sub-item">Listar Compras</RouterLink>
          </div>
        </div>

      </nav>

      <div class="sidebar-footer">
        <div class="user-info">
          <span class="user-avatar">👤</span>
          <span class="user-name">{{ auth.user?.name ?? 'Usuário' }}</span>
        </div>
        <button class="logout-btn" @click="handleLogout" title="Sair">⏻</button>
      </div>
    </aside>

    <!-- Conteúdo -->
    <main class="main-content">
      <RouterView />
    </main>
  </div>
</template>

<style scoped>
.app-shell {
  display: flex;
  min-height: 100vh;
  background: var(--color-bg);
}

/* ── Sidebar ────────────────────────────────────────────────────────────── */

.sidebar {
  width: 240px;
  min-height: 100vh;
  background: #1e1b4b;
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
  position: sticky;
  top: 0;
  height: 100vh;
}

.sidebar-brand {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 1.4rem 1.25rem;
  border-bottom: 1px solid rgba(255,255,255,0.08);
}

.brand-icon { font-size: 1.4rem; }

.brand-name {
  font-size: 1.1rem;
  font-weight: 700;
  color: #fff;
  letter-spacing: 0.02em;
}

/* ── Nav ────────────────────────────────────────────────────────────────── */

.sidebar-nav {
  flex: 1;
  padding: 1rem 0.75rem;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  overflow-y: auto;
}

.nav-group-btn {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.65rem 0.75rem;
  background: transparent;
  border: none;
  border-radius: 8px;
  color: rgba(255,255,255,0.75);
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  text-align: left;
  transition: background 0.15s, color 0.15s;
}

.nav-group-btn:hover {
  background: rgba(255,255,255,0.08);
  color: #fff;
}

.nav-icon { font-size: 1rem; }

.nav-chevron {
  margin-left: auto;
  font-size: 1.1rem;
  display: inline-block;
  transition: transform 0.2s;
  transform: rotate(0deg);
}

.nav-chevron.open {
  transform: rotate(90deg);
}

/* Submenu */
.nav-submenu {
  display: flex;
  flex-direction: column;
  overflow: hidden;
  max-height: 0;
  transition: max-height 0.25s ease;
}

.nav-submenu.open {
  max-height: 200px;
}

.nav-sub-item {
  padding: 0.5rem 0.75rem 0.5rem 2.4rem;
  color: rgba(255,255,255,0.55);
  font-size: 0.85rem;
  text-decoration: none;
  border-radius: 8px;
  transition: background 0.15s, color 0.15s;
}

.nav-sub-item:hover {
  background: rgba(255,255,255,0.07);
  color: rgba(255,255,255,0.9);
}

.nav-sub-item.router-link-active {
  background: rgba(99,102,241,0.3);
  color: #a5b4fc;
  font-weight: 500;
}

/* ── Footer ─────────────────────────────────────────────────────────────── */

.sidebar-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.25rem;
  border-top: 1px solid rgba(255,255,255,0.08);
}

.user-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  overflow: hidden;
}

.user-avatar { font-size: 1.1rem; }

.user-name {
  color: rgba(255,255,255,0.7);
  font-size: 0.82rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.logout-btn {
  background: transparent;
  border: none;
  color: rgba(255,255,255,0.45);
  font-size: 1.15rem;
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 6px;
  transition: color 0.15s, background 0.15s;
  flex-shrink: 0;
}

.logout-btn:hover {
  color: #f87171;
  background: rgba(248,113,113,0.1);
}

/* ── Main ────────────────────────────────────────────────────────────────── */

.main-content {
  flex: 1;
  padding: 2rem;
  overflow-y: auto;
}
</style>
