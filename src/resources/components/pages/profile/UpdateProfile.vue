<template>
  <div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ $t('profile.my_profile') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">
                <router-link :to="{ name: 'admin.dashboard' }">{{ $t('common.home') }}</router-link>
              </li>
              <li class="breadcrumb-item active">{{ $t('profile.profile') }}</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle"
                       src="/img/default-avatar.png"
                       alt="User profile picture">
                </div>

                <h3 class="profile-username text-center">{{ authStore.user?.name }}</h3>

                <p class="text-muted text-center">{{ authStore.user?.role }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>{{ $t('profile.email') }}</b> 
                    <span class="float-right">{{ authStore.user?.email }}</span>
                  </li>
                  <li class="list-group-item">
                    <b>{{ $t('profile.role') }}</b> 
                    <span class="float-right">
                      <span class="badge" :class="authStore.isAdmin ? 'badge-danger' : 'badge-info'">
                        {{ authStore.user?.role }}
                      </span>
                    </span>
                  </li>
                  <li class="list-group-item">
                    <b>{{ $t('profile.member_since') }}</b> 
                    <span class="float-right">{{ authStore.user?.formatted_created_at }}</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item">
                    <a class="nav-link" :class="{ active: activeTab === 'profile' }" 
                       href="#profile" 
                       @click="activeTab = 'profile'">
                      {{ $t('profile.update_profile') }}
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" :class="{ active: activeTab === 'password' }" 
                       href="#password" 
                       @click="activeTab = 'password'">
                      {{ $t('profile.change_password') }}
                    </a>
                  </li>
                </ul>
              </div>

              <div class="card-body">
                <!-- Profile Update Tab -->
                <div v-if="activeTab === 'profile'" class="tab-pane active">
                  <form @submit.prevent="updateProfile">
                    <div class="form-group row">
                      <label for="inputName" class="col-sm-2 col-form-label">{{ $t('profile.name') }}</label>
                      <div class="col-sm-10">
                        <input 
                          v-model="profileForm.name" 
                          type="text" 
                          class="form-control" 
                          :class="{ 'is-invalid': profileErrors.name }"
                          id="inputName" 
                          :placeholder="$t('profile.name')"
                          required
                        >
                        <div v-if="profileErrors.name" class="invalid-feedback">
                          {{ profileErrors.name[0] }}
                        </div>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label for="inputEmail" class="col-sm-2 col-form-label">{{ $t('profile.email') }}</label>
                      <div class="col-sm-10">
                        <input 
                          v-model="profileForm.email" 
                          type="email" 
                          class="form-control" 
                          :class="{ 'is-invalid': profileErrors.email }"
                          id="inputEmail" 
                          :placeholder="$t('profile.email')"
                          required
                        >
                        <div v-if="profileErrors.email" class="invalid-feedback">
                          {{ profileErrors.email[0] }}
                        </div>
                      </div>
                    </div>

                    <div class="form-group row">
                      <div class="offset-sm-2 col-sm-10">
                        <button type="submit" class="btn btn-primary" :disabled="authStore.isLoading">
                          <span v-if="authStore.isLoading" class="spinner-border spinner-border-sm me-2"></span>
                          {{ $t('profile.update_profile') }}
                        </button>
                      </div>
                    </div>
                  </form>
                </div>

                <!-- Password Change Tab -->
                <div v-if="activeTab === 'password'" class="tab-pane active">
                  <form @submit.prevent="changePassword">
                    <div class="form-group row">
                      <label for="currentPassword" class="col-sm-2 col-form-label">{{ $t('profile.current_password') }}</label>
                      <div class="col-sm-10">
                        <input 
                          v-model="passwordForm.current_password" 
                          type="password" 
                          class="form-control" 
                          :class="{ 'is-invalid': passwordErrors.current_password }"
                          id="currentPassword" 
                          :placeholder="$t('profile.current_password')"
                          required
                        >
                        <div v-if="passwordErrors.current_password" class="invalid-feedback">
                          {{ passwordErrors.current_password[0] }}
                        </div>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label for="newPassword" class="col-sm-2 col-form-label">{{ $t('profile.new_password') }}</label>
                      <div class="col-sm-10">
                        <input 
                          v-model="passwordForm.password" 
                          type="password" 
                          class="form-control" 
                          :class="{ 'is-invalid': passwordErrors.password }"
                          id="newPassword" 
                          :placeholder="$t('profile.new_password')"
                          required
                        >
                        <div v-if="passwordErrors.password" class="invalid-feedback">
                          {{ passwordErrors.password[0] }}
                        </div>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label for="confirmPassword" class="col-sm-2 col-form-label">{{ $t('profile.confirm_password') }}</label>
                      <div class="col-sm-10">
                        <input 
                          v-model="passwordForm.password_confirmation" 
                          type="password" 
                          class="form-control" 
                          :class="{ 'is-invalid': passwordErrors.password_confirmation }"
                          id="confirmPassword" 
                          :placeholder="$t('profile.confirm_password')"
                          required
                        >
                        <div v-if="passwordErrors.password_confirmation" class="invalid-feedback">
                          {{ passwordErrors.password_confirmation[0] }}
                        </div>
                      </div>
                    </div>

                    <div class="form-group row">
                      <div class="offset-sm-2 col-sm-10">
                        <button type="submit" class="btn btn-primary" :disabled="authStore.isLoading">
                          <span v-if="authStore.isLoading" class="spinner-border spinner-border-sm me-2"></span>
                          {{ $t('profile.change_password') }}
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, getCurrentInstance } from 'vue'
import { useAuthStore } from '../../../js/stores/auth'

const authStore = useAuthStore()

// Get global properties
const instance = getCurrentInstance()
const $t = instance.appContext.config.globalProperties.$t
const $toastr = instance.appContext.config.globalProperties.$toastr

// Active tab
const activeTab = ref('profile')

// Profile form
const profileForm = reactive({
  name: '',
  email: ''
})

// Password form
const passwordForm = reactive({
  current_password: '',
  password: '',
  password_confirmation: ''
})

// Error handling
const profileErrors = ref({})
const passwordErrors = ref({})

// Initialize profile form with user data
onMounted(() => {
  if (authStore.user) {
    profileForm.name = authStore.user.name
    profileForm.email = authStore.user.email
  }
})

const updateProfile = async () => {
  profileErrors.value = {}

  try {
    const result = await authStore.updateProfile(profileForm)
    
    if (result.success) {
      $toastr.success($t('profile.profile_updated_successfully'), $t('common.success'))
    } else {
      if (result.errors) {
        profileErrors.value = result.errors
      } else {
        $toastr.error(result.message, $t('common.error'))
      }
    }
  } catch (error) {
    console.error('Profile update error:', error)
    $toastr.error($t('profile.profile_update_failed'), $t('common.error'))
  }
}

const changePassword = async () => {
  passwordErrors.value = {}

  // Validate password confirmation
  if (passwordForm.password !== passwordForm.password_confirmation) {
    passwordErrors.value.password_confirmation = [$t('auth.password_confirmation_mismatch')]
    return
  }

  try {
    const result = await authStore.changePassword(passwordForm)
    
    if (result.success) {
      $toastr.success($t('profile.password_changed_successfully'), $t('common.success'))
      
      // Clear form
      passwordForm.current_password = ''
      passwordForm.password = ''
      passwordForm.password_confirmation = ''
    } else {
      if (result.errors) {
        passwordErrors.value = result.errors
      } else {
        $toastr.error(result.message, $t('common.error'))
      }
    }
  } catch (error) {
    console.error('Password change error:', error)
    $toastr.error($t('profile.password_change_failed'), $t('common.error'))
  }
}
</script>

<style scoped>
.profile-user-img {
  width: 100px;
  height: 100px;
  border: 3px solid #dee2e6;
  object-fit: cover;
}

.nav-pills .nav-link {
  color: #495057;
}

.nav-pills .nav-link.active {
  background-color: #007bff;
}

.card-primary.card-outline {
  border-top: 3px solid #007bff;
}

.badge-danger {
  background-color: #dc3545;
}

.badge-info {
  background-color: #17a2b8;
}
</style>
