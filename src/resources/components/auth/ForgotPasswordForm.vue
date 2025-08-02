<template>
  <div class="login-page" style="min-height: 466px;">
    <div class="login-box">
      <div class="card card-outline card-primary">
        <div class="card-header text-center">
          <a href="#" class="h1"><b>Admin</b>LTE</a>
        </div>
        <div class="card-body">
          <p class="login-box-msg">{{ t('auth.forgot_password_message') }}</p>

          <form @submit.prevent="handleForgotPassword">
            <div class="input-group mb-3">
              <input 
                v-model="form.email" 
                type="email" 
                class="form-control" 
                :class="{ 'is-invalid': errors.email }"
                :placeholder="t('auth.email')"
                required
              >
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-envelope"></span>
                </div>
              </div>
              <div v-if="errors.email" class="invalid-feedback">
                {{ errors.email[0] }}
              </div>
            </div>

            <div class="row">
              <div class="col-12">
                <button 
                  type="submit" 
                  class="btn btn-primary btn-block"
                  :disabled="isLoading"
                >
                  <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
                  {{ t('auth.send_reset_link') }}
                </button>
              </div>
            </div>
          </form>

          <p class="mt-3 mb-1">
            <router-link :to="{ name: 'auth.login' }">
              {{ t('auth.back_to_login') }}
            </router-link>
          </p>
        </div>
      </div>
    </div>

    <!-- Success Alert -->
    <div v-if="successMessage" class="alert alert-success alert-dismissible fade show position-fixed" 
         style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
      <strong>{{ t('common.success') }}!</strong> {{ successMessage }}
      <button type="button" class="close" @click="successMessage = ''" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>

    <!-- Error Alert -->
    <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show position-fixed" 
         style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
      <strong>{{ t('common.error') }}!</strong> {{ errorMessage }}
      <button type="button" class="close" @click="errorMessage = ''" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch, getCurrentInstance } from 'vue'
import { useI18n } from 'vue-i18n'
import axios from 'axios'

const { t } = useI18n()

// Get toastr from global properties
const instance = getCurrentInstance()
const $toastr = instance.appContext.config.globalProperties.$toastr

// Form data
const form = reactive({
  email: ''
})

// State
const isLoading = ref(false)
const errors = ref({})
const errorMessage = ref('')
const successMessage = ref('')

const handleForgotPassword = async () => {
  errors.value = {}
  errorMessage.value = ''
  successMessage.value = ''
  isLoading.value = true

  try {
    // This would be implemented when you add password reset functionality
    // For now, just show a success message
    await new Promise(resolve => setTimeout(resolve, 1000)) // Simulate API call
    
    successMessage.value = t('auth.reset_link_sent')
    $toastr.success(t('auth.reset_link_sent'), t('common.success'))
    
    // Clear form
    form.email = ''
    
  } catch (error) {
    console.error('Forgot password error:', error)
    errorMessage.value = t('auth.forgot_password_failed')
  } finally {
    isLoading.value = false
  }
}

// Clear errors when user types
const clearErrors = () => {
  errors.value = {}
  errorMessage.value = ''
  successMessage.value = ''
}

// Watch form changes to clear errors
watch(form, clearErrors)
</script>

<style scoped>
.login-page {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
}

.login-box {
  width: 360px;
  margin: auto;
}

.card {
  box-shadow: 0 0 20px rgba(0,0,0,0.1);
  border: none;
}

.card-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-bottom: none;
}

.card-header a {
  color: white;
  text-decoration: none;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
}

.input-group-text {
  background-color: #f8f9fa;
  border-left: none;
}

.form-control {
  border-right: none;
}

.form-control:focus {
  box-shadow: none;
  border-color: #667eea;
}
</style>
