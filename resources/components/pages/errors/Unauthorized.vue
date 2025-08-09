<template>
  <div class="content-wrapper">
    <section class="content">
      <div class="error-page">
        <h2 class="headline text-warning">403</h2>
        <div class="error-content">
          <h3><i class="fas fa-exclamation-triangle text-warning"></i> {{ $t('errors.unauthorized') }}</h3>
          <p>
            {{ $t('errors.unauthorized_message') }}
            <router-link :to="{ name: 'admin.dashboard' }">{{ $t('common.return_home') }}</router-link>
          </p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { getCurrentInstance } from 'vue'

// Get global properties
const instance = getCurrentInstance()
const $t = instance.appContext.config.globalProperties.$t
</script>

<style scoped>
.error-page {
  margin: 20px auto 0 auto;
  width: 600px;
  text-align: center;
}

.headline {
  float: left;
  font-size: 100px;
  font-weight: 300;
}

.error-content {
  margin-left: 190px;
  display: block;
}

.error-content h3 {
  font-weight: 300;
  font-size: 25px;
}
</style>
