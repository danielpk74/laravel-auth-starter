import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref(null)
  const token = ref(localStorage.getItem('auth_token'))
  const isLoading = ref(false)

  // Getters
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isAdmin = computed(() => user.value?.role === 'Admin')
  const isUser = computed(() => user.value?.role === 'User')

  // Actions
  const setToken = (newToken) => {
    token.value = newToken
    if (newToken) {
      localStorage.setItem('auth_token', newToken)
      axios.defaults.headers.common['Authorization'] = `Bearer ${newToken}`
    } else {
      localStorage.removeItem('auth_token')
      delete axios.defaults.headers.common['Authorization']
    }
  }

  const setUser = (userData) => {
    user.value = userData
  }

  const login = async (credentials) => {
    try {
      isLoading.value = true
      
      // First get CSRF cookie
      await axios.get('/sanctum/csrf-cookie')
      
      // Then attempt login
      const response = await axios.post('/api/auth/login', credentials)
      
      const { user: userData, token: authToken } = response.data.data
      
      setToken(authToken)
      setUser(userData)
      
      return { success: true, user: userData }
    } catch (error) {
      console.error('Login error:', error)
      const message = error.response?.data?.message || 'Login failed'
      return { success: false, message }
    } finally {
      isLoading.value = false
    }
  }

  const register = async (userData) => {
    try {
      isLoading.value = true
      
      // Get CSRF cookie
      await axios.get('/sanctum/csrf-cookie')
      
      const response = await axios.post('/api/auth/register', userData)
      
      const { user: newUser, token: authToken } = response.data.data
      
      setToken(authToken)
      setUser(newUser)
      
      return { success: true, user: newUser }
    } catch (error) {
      console.error('Registration error:', error)
      const message = error.response?.data?.message || 'Registration failed'
      return { success: false, message, errors: error.response?.data?.errors }
    } finally {
      isLoading.value = false
    }
  }

  const logout = async () => {
    try {
      if (token.value) {
        await axios.post('/api/auth/logout')
      }
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      setToken(null)
      setUser(null)
    }
  }

  const fetchUser = async () => {
    try {
      if (!token.value) return false
      
      const response = await axios.get('/api/auth/user')
      setUser(response.data.data)
      return true
    } catch (error) {
      console.error('Fetch user error:', error)
      // Token might be invalid, clear it
      setToken(null)
      setUser(null)
      return false
    }
  }

  const updateProfile = async (profileData) => {
    try {
      isLoading.value = true
      
      const response = await axios.put('/api/auth/profile', profileData)
      setUser(response.data.data)
      
      return { success: true, user: response.data.data }
    } catch (error) {
      console.error('Update profile error:', error)
      const message = error.response?.data?.message || 'Profile update failed'
      return { success: false, message, errors: error.response?.data?.errors }
    } finally {
      isLoading.value = false
    }
  }

  const changePassword = async (passwordData) => {
    try {
      isLoading.value = true
      
      await axios.put('/api/auth/password', passwordData)
      
      return { success: true, message: 'Password updated successfully' }
    } catch (error) {
      console.error('Change password error:', error)
      const message = error.response?.data?.message || 'Password change failed'
      return { success: false, message, errors: error.response?.data?.errors }
    } finally {
      isLoading.value = false
    }
  }

  // Initialize axios token if exists
  if (token.value) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
  }

  return {
    // State
    user,
    token,
    isLoading,
    // Getters
    isAuthenticated,
    isAdmin,
    isUser,
    // Actions
    login,
    register,
    logout,
    fetchUser,
    updateProfile,
    changePassword,
    setToken,
    setUser
  }
})
