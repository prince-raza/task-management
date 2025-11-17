<template>
  <div class="flex flex-col h-screen">
    <!-- Login Screen -->
    <div v-if="!isAuthenticated" class="flex items-center justify-center h-screen bg-gradient-to-br from-blue-500 to-blue-600">
      <div class="bg-white rounded-lg shadow-xl p-8 w-96">
        <div class="flex justify-center mb-6">
          <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
            <i class="fas fa-tasks text-white text-xl"></i>
          </div>
        </div>
        
        <h1 class="text-2xl font-bold text-center text-gray-900 mb-6">Task Manager</h1>
        
        <form @submit.prevent="handleLogin">
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-medium mb-2">Email</label>
            <input
              v-model="loginForm.email"
              type="email"
              placeholder="your@email.com"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            />
          </div>
          
          <div class="mb-6">
            <label class="block text-gray-700 text-sm font-medium mb-2">Password</label>
            <input
              v-model="loginForm.password"
              type="password"
              placeholder="••••••••"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            />
          </div>
          
          <button
            type="submit"
            :disabled="isLoggingIn"
            class="w-full bg-blue-500 text-white py-2 rounded-lg font-medium hover:bg-blue-600 transition disabled:opacity-50"
          >
            {{ isLoggingIn ? 'Logging in...' : 'Login' }}
          </button>
          
          <p v-if="loginError" class="text-red-500 text-sm mt-4 text-center">{{ loginError }}</p>
          
          <div class="mt-6 p-4 bg-gray-100 rounded-lg">
            <p class="text-xs text-gray-600 mb-2"><strong>Demo Credentials:</strong></p>
            <p class="text-xs text-gray-600">Email: Look in the terminal for seeded users</p>
            <p class="text-xs text-gray-600">Or use any email from the database</p>
          </div>
        </form>
      </div>
    </div>

    <!-- Main App -->
    <template v-else>
      <!-- Header -->
      <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="w-full px-4 py-1">
          <div class="flex items-center justify-between">
            <!-- Left: App Icon -->
            <div class="flex items-center">
              <div
                class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center mr-3"
              >
                <i class="fas fa-tasks text-white text-sm"></i>
              </div>
            </div>

            <!-- Middle: Search Bar -->
            <div class="relative mx-4 w-10 max-w-md flex-1">
              <input
                type="text"
                placeholder="Search tasks..."
                class="w-full py-0.5 px-4 pl-12 rounded-lg bg-grey/20 text-grey placeholder-grey-100 border border-gray-300 focus:outline-none focus:ring-0 focus:border-gray-300"
              />
              <i class="fas fa-search absolute left-4 top-3.5 text-black-100"></i>
            </div>

            <!-- Right: User Menu -->
            <div class="flex items-center gap-4">
              <div v-if="currentUser" class="text-sm text-gray-700">
                {{ currentUser.name }}
              </div>
              <div class="flex items-center">
                <button
                  @click="handleLogout"
                  class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center overflow-hidden hover:bg-gray-400 transition"
                  title="Logout"
                >
                  <i class="fas fa-sign-out-alt text-gray-600 text-sm"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Content -->
      <div class="flex flex-1 bg-gray-50">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 p-4 overflow-y-auto">
          <!-- Today Section -->
          <div class="mb-6">
            <div
              @click="selectedDate = today"
              :class="selectedDate === today ? 'bg-black text-white' : 'text-gray-700'"
              class="w-full px-2 py-1 text-sm rounded-lg font-medium cursor-pointer hover:bg-gray-100"
            >
              Today
            </div>
          </div>

          <!-- Quick Links -->
          <div class="space-y-1 mb-6" v-if="groupedDates.recent.length > 0">
            <div
              v-for="date in groupedDates.recent"
              :key="date"
              @click="selectedDate = date"
              :class="selectedDate === date ? 'bg-black text-white' : 'text-gray-700'"
              class="text-sm px-2 py-1 rounded cursor-pointer hover:bg-gray-100"
            >
              {{ formatDate(date) }}
            </div>
          </div>

          <!-- Last week -->
          <div class="mb-4" v-if="groupedDates.lastWeek.length > 0">
            <div class="text-gray-400 text-xs px-2 py-2 uppercase">Last week</div>
            <div class="space-y-1">
              <div
                v-for="date in groupedDates.lastWeek"
                :key="date"
                @click="selectedDate = date"
                :class="selectedDate === date ? 'bg-black text-white' : 'text-gray-700'"
                class="text-sm px-2 py-1 rounded cursor-pointer hover:bg-gray-100"
              >
                {{ formatDate(date) }}
              </div>
            </div>
          </div>

          <!-- Older dates -->
          <div class="mb-4" v-if="groupedDates.older.length > 0">
            <div class="text-gray-400 text-xs px-2 py-2 uppercase">Older</div>
            <div class="space-y-1">
              <div
                v-for="date in groupedDates.older"
                :key="date"
                @click="selectedDate = date"
                :class="selectedDate === date ? 'bg-black text-white' : 'text-gray-700'"
                class="text-sm px-2 py-1 rounded cursor-pointer hover:bg-gray-100"
              >
                {{ formatDate(date) }}
              </div>
            </div>
          </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col items-center justify-center p-8">
          <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-8">What do you have in mind?</h1>
            
            <!-- Input Box -->
            <div class="w-full max-w-2xl">
              <div class="relative">
                <textarea
                  v-model="newTask"
                  placeholder="Write the task you plan to do today here..."
                  class="w-full px-6 py-4 text-gray-700 placeholder-gray-400 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                  rows="4"
                ></textarea>
                <!-- Send Button -->
                <button
                  @click="addTask"
                  class="absolute bottom-3 right-3 w-10 h-10 bg-black rounded-full flex items-center justify-center text-white hover:bg-gray-800 transition"
                >
                  <i class="fas fa-arrow-up text-sm"></i>
                </button>
              </div>
            </div>
          </div>
        </main>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'

// Get API base URL from runtime config
const config = useRuntimeConfig()
const apiBase = config.public.apiBase

// Authentication state
const isAuthenticated = ref(false)
const isLoggingIn = ref(false)
const loginError = ref('')
const currentUser = ref(null)
const loginForm = ref({
  email: '',
  password: ''
})

// Task state
const newTask = ref('')
const selectedDate = ref(null)
const allDates = ref([])
const today = computed(() => new Date().toISOString().split('T')[0])

onMounted(() => {
  // Check if already authenticated
  const token = localStorage.getItem('auth_token')
  const user = localStorage.getItem('user')
  
  if (token && user) {
    isAuthenticated.value = true
    currentUser.value = JSON.parse(user)
    selectedDate.value = today.value
    fetchTaskDates()
  }
})

const handleLogin = async () => {
  isLoggingIn.value = true
  loginError.value = ''

  try {
    const response = await fetch(`${apiBase}/api/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        email: loginForm.value.email,
        password: loginForm.value.password
      })
    })

    const data = await response.json()

    if (response.ok && data.token) {
      // Save token and user to localStorage
      localStorage.setItem('auth_token', data.token)
      localStorage.setItem('user', JSON.stringify(data.user))
      
      isAuthenticated.value = true
      currentUser.value = data.user
      selectedDate.value = today.value
      
      // Clear form
      loginForm.value = { email: '', password: '' }
      
      // Fetch tasks
      fetchTaskDates()
    } else {
      loginError.value = data.message || 'Login failed'
    }
  } catch (error) {
    console.error('Login error:', error)
    loginError.value = 'An error occurred during login'
  } finally {
    isLoggingIn.value = false
  }
}

const handleLogout = () => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')
  isAuthenticated.value = false
  currentUser.value = null
  allDates.value = []
  newTask.value = ''
}

const fetchTaskDates = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    console.log('Token:', token)
    console.log('API Base:', apiBase)
    
    const response = await fetch(`${apiBase}/api/tasks`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })

    console.log('Response status:', response.status)

    if (response.ok) {
      const data = await response.json()
      console.log('Tasks data:', data)
      
      if (data.data && Array.isArray(data.data)) {
        // Extract dates from the date field (which is the task's assigned date)
        const dates = data.data
          .map((task) => {
            // Use the date field which is in YYYY-MM-DD format
            if (task.date) {
              return task.date
            }
            return null
          })
          .filter((date) => date !== null)
          .sort()
          .reverse()
        
        // Remove duplicates
        allDates.value = [...new Set(dates)]
        console.log('All dates from task.date field:', allDates.value)
      }
    } else {
      console.log('Response error:', response.status, await response.text())
      // Show sample data for testing
      showSampleDates()
    }
  } catch (error) {
    console.error('Error fetching tasks:', error)
    // Show sample data for testing
    showSampleDates()
  }
}

const showSampleDates = () => {
  const today = new Date().toISOString().split('T')[0]
  const dates = []
  
  // Add some sample dates
  for (let i = 0; i < 15; i++) {
    const date = new Date()
    date.setDate(date.getDate() - i)
    dates.push(date.toISOString().split('T')[0])
  }
  
  allDates.value = dates
  console.log('Using sample dates:', dates)
}

const formatDate = (dateString) => {
  const date = new Date(dateString + 'T00:00:00')
  return date.toLocaleDateString('en-US', {
    weekday: 'long',
    month: 'long',
    day: 'numeric'
  })
}

const groupedDates = computed(() => {
  const now = new Date()
  const todayDate = new Date(today.value)
  const oneWeekAgo = new Date(todayDate)
  oneWeekAgo.setDate(oneWeekAgo.getDate() - 7)

  const recent = []
  const lastWeek = []
  const older = []

  allDates.value.forEach((dateString) => {
    if (dateString === today.value) return // Skip today as it's shown separately

    const date = new Date(dateString + 'T00:00:00')

    if (date > oneWeekAgo && date < todayDate) {
      recent.push(dateString)
    } else if (date >= oneWeekAgo && date <= todayDate) {
      lastWeek.push(dateString)
    } else {
      older.push(dateString)
    }
  })

  return { recent, lastWeek, older }
})

const addTask = async () => {
  if (!newTask.value.trim()) return

  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch(`${apiBase}/api/tasks`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        description: newTask.value,
        date: selectedDate.value || today.value, // Optional: use selected date or today
        status: 'pending',
        priority: 'medium'
      })
    })

    if (response.ok) {
      newTask.value = ''
      // Refresh the dates list after adding a task
      await fetchTaskDates()
      console.log('Task added successfully')
    } else {
      console.error('Failed to add task:', response.status)
    }
  } catch (error) {
    console.error('Error adding task:', error)
  }
}
</script>
