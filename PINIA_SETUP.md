# Pinia State Management Setup Guide

## Overview

This project uses **Pinia**, a modern state management library for Vue 3. Pinia replaces the complexity of managing local component state and localStorage by providing a centralized, reactive store system.

## What is Pinia?

Pinia is a lightweight, type-safe state management solution for Vue 3 applications. It offers:

- **Centralized State**: All global state lives in one place (stores).
- **Reactive Updates**: Components automatically re-render when store state changes.
- **Modular Stores**: Each feature/domain can have its own store.
- **Persistence**: Easily persist state to localStorage or other storage.
- **DevTools Support**: Debug state changes with Vue DevTools.

## Installation Steps

### 1. Install Dependencies

```bash
cd frontend
pnpm install
```

This command installs all dependencies including:
- `pinia` — the state management library
- `@pinia/nuxt` — Nuxt integration plugin for Pinia

Both packages are already listed in `frontend/package.json` under `dependencies`.

### 2. Enable Pinia Module in Nuxt Config

Update `frontend/nuxt.config.ts`:

```typescript
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  modules: ['@nuxtjs/tailwindcss', '@pinia/nuxt'], // Add '@pinia/nuxt' here
  runtimeConfig: {
    public: {
      apiBase: 'http://localhost'
    }
  },
  // ... rest of config
})
```

**Why?** The `@pinia/nuxt` module auto-registers Pinia with the Nuxt app, so you don't need manual plugin setup. It also auto-discovers stores in the `stores/` directory.

### 3. Create Store Files

Stores are created in the `frontend/stores/` directory. Each store uses the Composition API syntax with `defineStore()`.

#### Example: Auth Store (`frontend/stores/auth.ts`)

```typescript
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  // State
  const token = ref<string | null>(null)
  const user = ref<any>(null)
  const isLoggingIn = ref(false)
  const loginError = ref('')

  // Actions
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
```

**Explanation:**
- `defineStore('auth', () => {})` — Creates a store named `'auth'`.
- `ref()` — Creates reactive state variables.
- Actions (functions) modify state directly or via side effects.
- Return object exposes all state and actions for use in components.

#### Example: Tasks Store (`frontend/stores/tasks.ts`)

```typescript
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useAuthStore } from './auth'

export const useTasksStore = defineStore('tasks', () => {
  const allDates = ref<string[]>([])

  const fetchTasks = async (apiBase: string) => {
    try {
      const auth = useAuthStore()
      const token = auth.token

      const response = await fetch(`${apiBase}/api/tasks`, {
        headers: {
          Authorization: `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      })

      if (response.ok) {
        const data = await response.json()
        if (data.data && Array.isArray(data.data)) {
          const dates = data.data
            .map((task: any) => task.date || null)
            .filter((d: any) => d !== null)
            .sort()
            .reverse()

          allDates.value = Array.from(new Set(dates))
          return
        }
      }

      showSampleDates()
    } catch (error) {
      console.error('Error fetching tasks in store:', error)
      showSampleDates()
    }
  }

  const addTask = async (apiBase: string, payload: any) => {
    try {
      const auth = useAuthStore()
      const token = auth.token

      const response = await fetch(`${apiBase}/api/tasks`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${token}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
      })

      if (response.ok) {
        await fetchTasks(apiBase)
        return true
      }

      return false
    } catch (error) {
      console.error('Error adding task in store:', error)
      return false
    }
  }

  const showSampleDates = () => {
    const today = new Date().toISOString().split('T')[0]
    const dates: string[] = []
    for (let i = 0; i < 15; i++) {
      const date = new Date()
      date.setDate(date.getDate() - i)
      dates.push(date.toISOString().split('T')[0])
    }
    allDates.value = dates
  }

  const groupedDates = computed(() => {
    const todayVal = new Date().toISOString().split('T')[0]
    const oneWeekAgo = new Date(todayVal)
    oneWeekAgo.setDate(oneWeekAgo.getDate() - 7)

    const recent: string[] = []
    const lastWeek: string[] = []
    const older: string[] = []

    allDates.value.forEach((dateString) => {
      if (dateString === todayVal) return
      const date = new Date(dateString + 'T00:00:00')

      if (date > oneWeekAgo && date < new Date(todayVal + 'T00:00:00')) {
        recent.push(dateString)
      } else if (date >= oneWeekAgo && date <= new Date(todayVal + 'T00:00:00')) {
        lastWeek.push(dateString)
      } else {
        older.push(dateString)
      }
    })

    return { recent, lastWeek, older }
  })

  return {
    allDates,
    fetchTasks,
    addTask,
    showSampleDates,
    groupedDates
  }
})
```

**Explanation:**
- Stores can depend on other stores (e.g., `useTasksStore` uses `useAuthStore`).
- `computed()` creates derived state that updates reactively.
- Actions can be async (e.g., `fetchTasks`).

### 4. Use Stores in Components

In any Vue component (e.g., `frontend/app/app.vue`):

```vue
<script setup>
import { computed, ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useTasksStore } from '../stores/tasks'

const config = useRuntimeConfig()
const apiBase = config.public.apiBase

// Initialize stores
const auth = useAuthStore()
const tasks = useTasksStore()

// Use computed to create reactive references to store state
const isAuthenticated = computed(() => auth.isAuthenticated())
const currentUser = computed(() => auth.user)
const groupedDates = computed(() => tasks.groupedDates)

// Component-local state
const loginForm = ref({ email: '', password: '' })
const newTask = ref('')

// Lifecycle hook
onMounted(async () => {
  auth.init() // Initialize auth from localStorage
  if (auth.isAuthenticated()) {
    await tasks.fetchTasks(apiBase)
  }
})

// Use store actions
const handleLogin = async () => {
  // ... login logic
  auth.setAuth(token, user) // Store token and user
  await tasks.fetchTasks(apiBase) // Fetch tasks
}

const handleLogout = () => {
  auth.clearAuth() // Clear auth state
  tasks.allDates = [] // Clear tasks
}

const addTask = async () => {
  const ok = await tasks.addTask(apiBase, payload)
  if (ok) {
    newTask.value = ''
  }
}
</script>
```

**Explanation:**
- `useAuthStore()` and `useTasksStore()` are called to get store instances.
- Use `computed()` to reactively bind store state to component templates.
- Call store actions (methods) to modify state.
- Components automatically re-render when store state changes.

## Store Architecture

### Auth Store (`stores/auth.ts`)

**Purpose:** Manage authentication state and token/user persistence.

**State:**
- `token` — Bearer token from API login.
- `user` — Authenticated user object.
- `isLoggingIn` — Loading flag for login form.
- `loginError` — Error message from failed login.

**Actions:**
- `init()` — Restore token/user from localStorage on app load.
- `setAuth(token, user)` — Store token and user in state and localStorage.
- `clearAuth()` — Clear token/user on logout.
- `isAuthenticated()` — Check if user is logged in.

### Tasks Store (`stores/tasks.ts`)

**Purpose:** Manage task data and date grouping.

**State:**
- `allDates` — Array of unique task dates from API.

**Actions:**
- `fetchTasks(apiBase)` — Fetch tasks from API and extract unique dates.
- `addTask(apiBase, payload)` — Create a new task via API, then refresh task list.
- `showSampleDates()` — Generate sample dates for testing without API.

**Computed:**
- `groupedDates` — Computed property that groups `allDates` into `recent`, `lastWeek`, and `older`.

## Key Concepts

### State
Reactive variables that hold application data. Use `ref()` for primitives/objects.

```typescript
const token = ref<string | null>(null)
const user = ref<any>(null)
```

### Actions
Functions that modify state or perform side effects. They can be sync or async.

```typescript
const setAuth = (t: string, u: any) => {
  token.value = t
  user.value = u
  localStorage.setItem('auth_token', t)
}
```

### Computed
Derived state that updates reactively when dependencies change.

```typescript
const groupedDates = computed(() => {
  // Returns { recent, lastWeek, older }
})
```

## Benefits of Using Pinia

1. **Centralized State** — No more passing props through multiple layers; access state directly from any component.
2. **Reactivity** — Vue's reactivity system automatically updates templates when store state changes.
3. **Async Actions** — Easily handle API calls within store actions; components don't need to manage loading states.
4. **Persistence** — Store actions can save state to localStorage or other backends.
5. **Type Safety** — Full TypeScript support for type checking.
6. **DevTools** — Debug store state and track action history with Vue DevTools.

## Running the Application

```bash
# Start frontend dev server
cd frontend
NUXT_VITE_HMR_PORT=24679 pnpm run dev

# In another terminal, start Laravel backend
cd backend
sail up -d
```

Open `http://localhost:3001/` (or the port shown in the terminal).

## File Structure

```
frontend/
├── stores/
│   ├── auth.ts         # Authentication store
│   └── tasks.ts        # Tasks/dates store
├── app/
│   └── app.vue         # Main app component (uses stores)
├── nuxt.config.ts      # Nuxt config with @pinia/nuxt module
└── package.json        # Dependencies including pinia
```

## Next Steps

- **Add More Stores** — Create additional stores for features (e.g., `useUIStore` for theme, modals).
- **Persistence Plugin** — Use `@pinia/plugin-persistedstate` to auto-persist stores.
- **Tests** — Write unit tests for store actions using Vitest.
- **Devtools** — Install Vue DevTools browser extension to debug store state in real-time.

## Troubleshooting

### Module Not Found Error
If you see `Cannot find module '~/stores/auth'`, change imports to relative paths:

```typescript
// ❌ Won't work without vite-tsconfig-paths
import { useAuthStore } from '~/stores/auth'

// ✅ Works with relative imports
import { useAuthStore } from '../stores/auth'
```

### State Not Persisting
Ensure `localStorage.setItem()` is called in store actions when you want to persist data:

```typescript
const setAuth = (t: string, u: any) => {
  token.value = t
  localStorage.setItem('auth_token', t) // Persist to storage
}
```

### Components Not Updating
Use `computed()` to wrap store state in components so Vue's reactivity system tracks changes:

```typescript
const currentUser = computed(() => auth.user) // ✅ Reactive
const currentUser = auth.user // ❌ Not reactive in template
```

## References

- [Pinia Official Docs](https://pinia.vuejs.org/)
- [Nuxt + Pinia Integration](https://pinia.vuejs.org/cookbook/composables.html)
- [Vue 3 Composition API](https://vuejs.org/guide/extras/composition-api-faq.html)
