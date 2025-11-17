import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(null)
  const user = ref<any>(null)
  const isLoggingIn = ref(false)
  const loginError = ref('')

  const init = () => {
    const t = localStorage.getItem('auth_token')
    const u = localStorage.getItem('user')
    if (t && u) {
      token.value = t
      try {
        user.value = JSON.parse(u)
      } catch {
        user.value = null
      }
    }
  }

  const setAuth = (t: string, u: any) => {
    token.value = t
    user.value = u
    localStorage.setItem('auth_token', t)
    localStorage.setItem('user', JSON.stringify(u))
  }

  const clearAuth = () => {
    token.value = null
    user.value = null
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user')
  }

  const isAuthenticated = () => {
    return !!token.value && !!user.value
  }

  return {
    token,
    user,
    isLoggingIn,
    loginError,
    init,
    setAuth,
    clearAuth,
    isAuthenticated
  }
})
