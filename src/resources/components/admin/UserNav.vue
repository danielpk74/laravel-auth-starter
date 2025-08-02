<template>
  <!-- User Dropdown -->
  <li class="nav-item dropdown user-menu">
    <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
      <img src="/img/default-avatar.png" class="user-image img-circle elevation-2" alt="User Image">
      <span class="d-none d-md-inline">{{ authStore.user?.name }}</span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
      <!-- User image -->
      <div class="dropdown-header text-center">
        <img src="/img/default-avatar.png" class="img-circle elevation-2" alt="User Image" style="width: 60px; height: 60px;">
        <h6 class="mt-2">{{ authStore.user?.name }}</h6>
        <small>{{ authStore.user?.email }}</small>
        <small class="d-block">
          <span class="badge" :class="authStore.isAdmin ? 'badge-danger' : 'badge-info'">
            {{ authStore.user?.role }}
          </span>
        </small>
      </div>
      <div class="dropdown-divider"></div>
      
      <!-- Menu Body -->
      <router-link :to="{ name: 'admin.profile' }" class="dropdown-item">
        <i class="fas fa-user mr-2"></i> {{ $t('profile.profile') }}
      </router-link>
      <router-link :to="{ name: 'admin.settings' }" class="dropdown-item" v-if="authStore.isAdmin">
        <i class="fas fa-cogs mr-2"></i> {{ $t('common.settings') }}
      </router-link>
      <div class="dropdown-divider"></div>
      
      <!-- Logout -->
      <a href="#" class="dropdown-item" @click.prevent="handleLogout">
        <i class="fas fa-sign-out-alt mr-2"></i> {{ $t('auth.logout') }}
      </a>
    </div>
  </li>
</template>

<script setup>
import { getCurrentInstance } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../js/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

// Get global properties
const instance = getCurrentInstance()
const $t = instance.appContext.config.globalProperties.$t
const $toastr = instance.appContext.config.globalProperties.$toastr

const handleLogout = async () => {
  try {
    await authStore.logout()
    $toastr.success($t('auth.logout_successful'), $t('common.success'))
    router.push({ name: 'auth.login' })
  } catch (error) {
    console.error('Logout error:', error)
    $toastr.error($t('auth.logout_failed'), $t('common.error'))
  }
}
</script>

<style scoped>
.user-image {
  width: 25px;
  height: 25px;
  margin-right: 10px;
  margin-top: -2px;
}

.dropdown-menu {
  min-width: 280px;
}

.dropdown-header {
  padding: 15px;
  background-color: #f8f9fa;
}
</style>
