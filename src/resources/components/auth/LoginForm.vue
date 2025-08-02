<template>
  <div class="login-page" style="min-height: 466px;">
    <div class="login-box">
      <div class="card card-outline card-primary">
        <div class="card-header text-center">
          <a href="#" class="h1"><b>Admin</b>LTE</a>
        </div>
        <div class="card-body">
          <p class="login-box-msg">{{ t('auth.sign_in_message') }}</p>

          <form @submit.prevent="handleLogin">
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

            <div class="input-group mb-3">
              <input 
                v-model="form.password" 
                type="password" 
                class="form-control" 
                :class="{ 'is-invalid': errors.password }"
                :placeholder="t('auth.password')"
                required
              >
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
              <div v-if="errors.password" class="invalid-feedback">
                {{ errors.password[0] }}
              </div>
            </div>

            <div class="row">
              <div class="col-8">
                <div class="icheck-primary">
                  <input v-model="form.remember" type="checkbox" id="remember">
                  <label for="remember">
                    {{ t('auth.remember_me') }}
                  </label>
                </div>
              </div>
              <div class="col-4">
                <button 
                  type="submit" 
                  class="btn btn-primary btn-block"
                  :disabled="authStore.isLoading"
                >
                  <span v-if="authStore.isLoading" class="spinner-border spinner-border-sm me-2"></span>
                  {{ t('auth.sign_in') }}
                </button>
              </div>
            </div>
          </form>

          <div class="social-auth-links text-center mt-2 mb-3">
            <p>- {{ t('auth.or') }} -</p>
            <router-link :to="{ name: 'auth.register' }" class="btn btn-block btn-outline-secondary">
              <i class="fas fa-user-plus mr-2"></i> {{ t('auth.register_new_membership') }}
            </router-link>
          </div>

          <p class="mb-1">
            <router-link :to="{ name: 'auth.forgot-password' }">
              {{ t('auth.forgot_password') }}
            </router-link>
          </p>
        </div>
      </div>
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
import { ref, reactive, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '../../js/stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const { t } = useI18n()

// Get toastr from global properties
import { getCurrentInstance } from 'vue'
const instance = getCurrentInstance()
const $toastr = instance.appContext.config.globalProperties.$toastr

// Form data
const form = reactive({
  email: '',
  password: '',
  remember: false
})

// Error handling
const errors = ref({})
const errorMessage = ref('')

const handleLogin = async () => {
  errors.value = {}
  errorMessage.value = ''

  try {
    const result = await authStore.login(form)
    
    if (result.success) {
      $toastr.success(t('auth.login_successful'), t('common.success'))
      
      // Redirect based on user role
      if (authStore.isAdmin) {
        router.push({ name: 'admin.dashboard' })
      } else {
        router.push({ name: 'user.dashboard' })
      }
    } else {
      if (result.errors) {
        errors.value = result.errors
      } else {
        errorMessage.value = result.message
      }
    }
  } catch (error) {
    console.error('Login error:', error)
    errorMessage.value = t('auth.login_failed')
  }
}

// Clear errors when user types
const clearErrors = () => {
  errors.value = {}
  errorMessage.value = ''
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
