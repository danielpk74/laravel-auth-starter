import { useAuthStore } from '../stores/auth'

export const requireAuth = (to, from, next) => {
  const authStore = useAuthStore()
  
  if (!authStore.isAuthenticated) {
    next({ name: 'auth.login' })
  } else {
    next()
  }
}

export const requireGuest = (to, from, next) => {
  const authStore = useAuthStore()
  
  if (authStore.isAuthenticated) {
    if (authStore.isAdmin) {
      next({ name: 'admin.dashboard' })
    } else {
      next({ name: 'user.dashboard' })
    }
  } else {
    next()
  }
}

export const requireAdmin = (to, from, next) => {
  const authStore = useAuthStore()
  
  if (!authStore.isAuthenticated) {
    next({ name: 'auth.login' })
  } else if (!authStore.isAdmin) {
    next({ name: 'unauthorized' })
  } else {
    next()
  }
}

export const requireRole = (roles) => {
  return (to, from, next) => {
    const authStore = useAuthStore()
    
    if (!authStore.isAuthenticated) {
      next({ name: 'auth.login' })
      return
    }
    
    const userRole = authStore.user?.role
    if (!roles.includes(userRole)) {
      next({ name: 'unauthorized' })
      return
    }
    
    next()
  }
}
