<template>
  <div class="register-page" style="min-height: 466px;">
    <div class="register-box">
      <div class="card card-outline card-primary">
        <div class="card-header text-center">
          <a href="#" class="h1"><b>Admin</b>LTE</a>
        </div>
        <div class="card-body">
          <p class="login-box-msg">{{ t('auth.register_new_membership') }}</p>

          <form @submit.prevent="handleRegister">
            <div class="input-group mb-3">
              <input 
                v-model="form.name" 
                type="text" 
                class="form-control" 
                :class="{ 'is-invalid': errors.name }"
                :placeholder="$t('auth.full_name')"
                required
              >
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-user"></span>
                </div>
              </div>
              <div v-if="errors.name" class="invalid-feedback">
                {{ errors.name[0] }}
              </div>
            </div>

            <div class="input-group mb-3">
              <input 
                v-model="form.email" 
                type="email" 
                class="form-control" 
                :class="{ 'is-invalid': errors.email }"
                :placeholder="$t('auth.email')"
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
                :placeholder="$t('auth.password')"
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

            <div class="input-group mb-3">
              <input 
                v-model="form.password_confirmation" 
                type="password" 
                class="form-control" 
                :class="{ 'is-invalid': errors.password_confirmation }"
                :placeholder="$t('auth.confirm_password')"
                required
              >
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
              <div v-if="errors.password_confirmation" class="invalid-feedback">
                {{ errors.password_confirmation[0] }}
              </div>
            </div>

            <div class="row">
              <div class="col-8">
                <div class="icheck-primary">
                  <input 
                    v-model="form.agree_terms" 
                    type="checkbox" 
                    id="agreeTerms"
                    :class="{ 'is-invalid': errors.agree_terms }"
                    required
                  >
                  <label for="agreeTerms">
                    {{ $t('auth.agree_terms') }}
                    <a href="#">{{ $t('auth.terms') }}</a>
                  </label>
                  <div v-if="errors.agree_terms" class="invalid-feedback d-block">
                    {{ errors.agree_terms[0] }}
                  </div>
                </div>
              </div>
              <div class="col-4">
                <button 
                  type="submit" 
                  class="btn btn-primary btn-block"
                  :disabled="authStore.isLoading"
                >
                  <span v-if="authStore.isLoading" class="spinner-border spinner-border-sm me-2"></span>
                  {{ $t('auth.register') }}
                </button>
              </div>
            </div>
          </form>

          <div class="social-auth-links text-center mt-3">
            <router-link :to="{ name: 'auth.login' }" class="text-center">
              {{ $t('auth.already_have_membership') }}
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Error Alert -->
    <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show position-fixed" 
         style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
      <strong>{{ $t('common.error') }}!</strong> {{ errorMessage }}
      <button type="button" class="close" @click="errorMessage = ''" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch, getCurrentInstance } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '../../js/stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const { t } = useI18n()

// Get toastr from global properties
const instance = getCurrentInstance()
const $toastr = instance.appContext.config.globalProperties.$toastr

// Form data
const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  agree_terms: false
})

// Error handling
const errors = ref({})
const errorMessage = ref('')

const handleRegister = async () => {
  errors.value = {}
  errorMessage.value = ''

  // Validate password confirmation
  if (form.password !== form.password_confirmation) {
    errors.value.password_confirmation = [t('auth.password_confirmation_mismatch')]
    return
  }

  if (!form.agree_terms) {
    errors.value.agree_terms = [t('auth.must_agree_terms')]
    return
  }

  try {
    const result = await authStore.register(form)
    
    if (result.success) {
      $toastr.success(t('auth.registration_successful'), t('common.success'))
      
      // Redirect to dashboard
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
    console.error('Registration error:', error)
    errorMessage.value = t('auth.registration_failed')
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
.register-page {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
}

.register-box {
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
